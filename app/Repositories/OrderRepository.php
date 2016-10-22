<?php

namespace App\Repositories;


use App\Models\Order;

class OrderRepository
{
    public function updateOrderWhenPayPalPaymentCreated($order, $payPalPayment)
    {
        $order->payPalPayments()->save($payPalPayment);
        $order->currentPayPalPayment()->associate($payPalPayment);
        $order->save();
    }

    public function updateOrderWhenPaid($order, $paidAmount)
    {
        $order['paid_amount'] = $paidAmount;
        $order['status'] = Order::PAID;
        $order->save();

        return $order;
    }

    public function create($user, $product, $price, $paymentType)
    {
        $order = new Order();
        $order['order_no'] = Order::generateOrderNo();
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

    public function update()
    {

    }
}