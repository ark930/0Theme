<?php

namespace App\Services;

use App\Exceptions\ServiceException;
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

    public function __construct(OrderRepository $orderRepository, PayPalPaymentRepository $palPaymentRepository,
        ProductRepository $productRepository, UserRepository $userRepository, PayPal $payPal)
    {
        $this->orderRepository = $orderRepository;
        $this->palPaymentRepository = $palPaymentRepository;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->payPal = $payPal;
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

    public function paid($payerId, $payPalPayment, $payment)
    {
        $paymentArray = json_decode($payment, true);

        $payPalPayment = $this->palPaymentRepository->updateFromPayPalResponse($payerId, $payPalPayment, $payment);

        $order = $payPalPayment->order;
        $paidAmount = $paymentArray['transactions'][0]['related_resources'][0]['sale']['amount']['total'];
        $order = $this->orderRepository->updateOrderWhenPaid($order, $paidAmount);

        return $order;
    }

    public function canceled()
    {
//        $order['status'] = Order::PAID;
//        $order->save();
    }
}