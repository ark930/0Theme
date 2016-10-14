<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\PayPalPayment;
use PayPal\Api\Payment;

class OrderHandler
{
    public function create($user, $product, $paymentType)
    {
        $orderNo = Order::makeOrderNo();

        $price = $product['price'];
        $productHandler = new ProductHandler();
        if($productHandler->canDiscount($user, $product)) {
            $price = $productHandler->priceWithDiscount($product);
        }

        $order = new Order();
        $order['order_no'] = $orderNo;
        $order['payment_type'] = $paymentType;
        $order['price'] = $price;
        $order['pay_amount'] = $price;
        $order['status'] = Order::UNPAY;
        $order->user()->associate($user);
        $order->product()->associate($product);
        $order->save();

        return $order;
    }

    public function paid($paidAmount)
    {
        $order['paid_amount'] = $paidAmount;
        $order['status'] = Order::PAID;
        $order->save();
    }

    public function canceled()
    {
//        $order['status'] = Order::PAID;
//        $order->save();
    }

    public function savePayPalPayment(Order $order, Payment $payment)
    {
        $paymentDataArray = json_decode($payment, true);

        $payPalPayment = new PayPalPayment();
        $payPalPayment['payment_id'] = $paymentDataArray['id'];
        $payPalPayment['payment_method'] = $paymentDataArray['payer']['payment_method'];
        $payPalPayment['intent'] = $paymentDataArray['intent'];
        $payPalPayment['amount'] = $paymentDataArray['transactions'][0]['amount']['total'];
        $payPalPayment['currency'] = $paymentDataArray['transactions'][0]['amount']['currency'];
        $payPalPayment['payment_state'] = $paymentDataArray['state'];
        $payPalPayment['create_json'] = $payment;
        $payPalPayment['approval_url'] = $paymentDataArray['links'][1]['href'];
        $payPalPayment['payment_create_at'] = date('Y-m-d H:i:s', strtotime($paymentDataArray['create_time']));

        $order->payPalPayments()->save($payPalPayment);
        $order->currentPayPalPayment()->associate($payPalPayment);
        $order->save();
    }
}