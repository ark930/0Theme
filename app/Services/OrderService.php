<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Repositories\PayPal;
use App\Repositories\PayPalPaymentRepository;
use App\Repositories\ProductHandler;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;

class OrderService
{
    protected $palPaymentRepository;
    protected $orderRepository;
    protected $productRepository;
    protected $userRepository;
    protected $payPal;
    protected $planService;

    public function __construct(OrderRepository $orderRepository, PayPalPaymentRepository $palPaymentRepository,
        ProductRepository $productRepository, UserRepository $userRepository, PayPal $payPal,
        PlanService $planService)
    {
        $this->orderRepository = $orderRepository;
        $this->palPaymentRepository = $palPaymentRepository;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->payPal = $payPal;
        $this->planService = $planService;
    }

    public function create($user, $productId, $paymentType, $baseUrl)
    {
        $product = $this->productRepository->get($productId);

        if(empty($product)) {
            if($this->productRepository->isThemeProduct($product)) {
                throw new ServiceException('Please select a theme', 400, true);
            } else {
                throw new ServiceException('Illegal parameters', 400, true);
            }
        }

        if($this->userRepository->isLifetimeUser($user)) {
            throw new ServiceException('You are already a LIFETIME user', 400, true);
        }

        $price = $product['price'];
        $productHandler = new ProductHandler();
        if($productHandler->canDiscount($user, $product)) {
            $price = $productHandler->priceWithDiscount($product);
        }

        $order = $this->orderRepository->create($user, $product, $price, $paymentType);

        $successUrl = sprintf('%s/%s?oid=%s', $baseUrl, 'payment/success', $order['order_no']);
        $failUrl = sprintf('%s/%s?oid=%s', $baseUrl, 'payment/fail', $order['order_no']);

        $payment = $this->payPal->createPaymentUsingPayPal($product['name'], $order['order_no'], $order['pay_amount'], $successUrl, $failUrl);

        $payPalPayment = $this->palPaymentRepository->createFromPayPalResponse($order, $payment);
        $this->orderRepository->updateOrderWhenPayPalPaymentCreated($order, $payPalPayment);

        $approvalLink = $payment->getApprovalLink();

        return $approvalLink;
    }

//    public function savePayPalPayment(Order $order, Payment $payment)
//    {
//        $payPalPayment = $this->palPaymentRepository->createFromPayPalResponse($order, $payment);
//        $this->orderRepository->updateOrderWhenPayPalPaymentCreated($order, $payPalPayment);
//    }

    public function paid($payerId, $paymentId, $orderNo)
    {
        $payPalPayment = $this->palPaymentRepository->getByPaymentId($paymentId);
        if(empty($payPalPayment)) {
            return 'error url 1';
        }

        $order = $payPalPayment->order;
        if($order['order_no'] != $orderNo) {
            return 'error url 2';
        }

        if($order['status'] == Order::PAID || $order['status'] == Order::REFUNDED) {
            return 'paid';
        }

        $total = $payPalPayment['amount'];
        $payment = $this->payPal->executePayment($paymentId, $payerId, $total);

        $paymentArray = json_decode($payment, true);

        $payPalPayment = $this->palPaymentRepository->updateFromPayPalResponse($payerId, $payPalPayment, $payment);

        $order = $payPalPayment->order;
        $paidAmount = $paymentArray['transactions'][0]['related_resources'][0]['sale']['amount']['total'];
        $order = $this->orderRepository->updateOrderWhenPaid($order, $paidAmount);

        $product = $order->product;
        $user = $order->user;
        $data = [
            'membership' => null,
            'period' => null,
            'account' => $user['email'],
            'total' => $order['paid_amount'],
        ];

        if($product['type'] == Product::TYPE_LIFETIME) {
            $this->planService->doLifetimePlan($user);

            $data['membership'] = User::MEMBERSHIP_LIFETIME;
            $data['period'] = 'Forever';
        } else if($product['type'] == Product::TYPE_PRO) {
            $this->planService->doProPlan($user);

            $data['membership'] = User::MEMBERSHIP_PRO;
            $data['period'] = '1 Year';
        } else if($product['type'] == Product::TYPE_THEME) {
            $this->planService->doBasicPlan($user, $product);

            $data['membership'] = User::MEMBERSHIP_BASIC;
            $data['period'] = '1 Year';
            $data['themeName'] = $product->theme['name'];
        }

        return $data;
    }

    public function canceled($orderNo)
    {
        $order = $this->orderRepository->getByOrderNo($orderNo);

        if(empty($orderNo)) {
            return 'error url';
        }

        if($order['status'] == Order::CANCELLED) {
            return 'cancelled';
        }

        if($order['status'] != Order::UNPAY) {
            return 'error url2';
        }

        $order = $this->orderRepository->updateOrderWhenPayFail($order);

        return $order;
    }
}