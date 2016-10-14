<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\PayPalPayment;
use PayPal\Api\Payment;

class OrderHandler
{
    public static function create($user, $product, $paymentType)
    {
        $orderNo = Order::makeOrderNo();

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

    public static function savePayPalPayment(Order $order, Payment $payment)
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

    public static function paid($payerId, $payPalPayment, $payment)
    {
        $paymentArray = json_decode($payment, true);

        $payPalPayment['payment_state'] = $paymentArray['state'];
        $payPalPayment['execute_json'] = $payment;
        $payPalPayment['payer_id'] = $payerId;
        $payPalPayment['sale_id'] = $paymentArray['transactions'][0]['related_resources'][0]['sale']['id'];
        $payPalPayment['sale_state'] = $paymentArray['transactions'][0]['related_resources'][0]['sale']['state'];
        $payPalPayment['transaction_fee'] = $paymentArray['transactions'][0]['related_resources'][0]['sale']['transaction_fee']['value'];
        $payPalPayment['payment_execute_at'] = date('Y-m-d H:i:s', strtotime($paymentArray['create_time']));
        $payPalPayment->save();

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