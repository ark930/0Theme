<?php

namespace App\Repositories;

use App\Models\Order;
use PayPal\Api\Payment;

class OrderHandler
{
    protected $palPaymentRepository;

    public function __construct(PayPalPaymentRepository $palPaymentRepository)
    {
        $this->palPaymentRepository = $palPaymentRepository;
    }

    public function create($user, $product, $paymentType)
    {
        $orderNo = Order::generateOrderNo();

        $price = $product['price'];
        $productHandler = new ProductHandler();
        if($productHandler->canDiscount($user, $product)) {
            $price = $productHandler->priceWithDiscount($product);
        }

        $order = new Order();
        $order['order_no'] = $orderNo;
        $order['name'] = $product['name'];
        $order['payment_type'] = $paymentType;
        $order['price'] = $price;
        $order['pay_amount'] = $price;
        $order['status'] = Order::UNPAY;
        $order->user()->associate($user);
        $order->product()->associate($product);
        $order->save();

        return $order;
    }

    public function savePayPalPayment(Order $order, Payment $payment)
    {
        $payPalPayment = $this->palPaymentRepository->createFromPayPalResponse($payment);

        $order->payPalPayments()->save($payPalPayment);
        $order->currentPayPalPayment()->associate($payPalPayment);
        $order->save();
    }

    public function paid($payerId, $payPalPayment, $payment)
    {
        $paymentArray = json_decode($payment, true);

        $payPalPayment = $this->palPaymentRepository->updateFromPayPalResponse($payerId, $payPalPayment, $payment);

        $order = $payPalPayment->order;
        $order['paid_amount'] = $paymentArray['transactions'][0]['related_resources'][0]['sale']['amount']['total'];
        $order['status'] = Order::PAID;
        $order->save();

        return $order;
    }

    public function canceled()
    {
//        $order['status'] = Order::PAID;
//        $order->save();
    }
}