<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\PayPalPaymentRepository;
use App\Repositories\ProductHandler;
use PayPal\Api\Payment;

class OrderService
{
    protected $palPaymentRepository;
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository, PayPalPaymentRepository $palPaymentRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->palPaymentRepository = $palPaymentRepository;
    }

    public function create($user, $product, $paymentType)
    {
        $price = $product['price'];
        $productHandler = new ProductHandler();
        if($productHandler->canDiscount($user, $product)) {
            $price = $productHandler->priceWithDiscount($product);
        }

        $order = $this->orderRepository->create($user, $product, $price, $paymentType);

        return $order;
    }

    public function savePayPalPayment(Order $order, Payment $payment)
    {
        $payPalPayment = $this->palPaymentRepository->createFromPayPalResponse($order, $payment);
        $this->orderRepository->updateOrderWhenPayPalPaymentCreated($order, $payPalPayment);
    }

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