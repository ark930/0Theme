<?php

namespace App\Repositories;


use App\Models\PayPalPayment;

class PayPalPaymentRepository
{
    public function createFromPayPalResponse($order, $payment)
    {
        $paymentDataArray = json_decode($payment, true);

        $payPalPaymentData = [];
        $payPalPaymentData['order_id'] = $order['id'];
        $payPalPaymentData['payment_id'] = $paymentDataArray['id'];
        $payPalPaymentData['payment_method'] = $paymentDataArray['payer']['payment_method'];
        $payPalPaymentData['intent'] = $paymentDataArray['intent'];
        $payPalPaymentData['amount'] = $paymentDataArray['transactions'][0]['amount']['total'];
        $payPalPaymentData['currency'] = $paymentDataArray['transactions'][0]['amount']['currency'];
        $payPalPaymentData['payment_state'] = $paymentDataArray['state'];
        $payPalPaymentData['create_json'] = $payment;
        $payPalPaymentData['approval_url'] = $paymentDataArray['links'][1]['href'];
        $payPalPaymentData['payment_create_at'] = date('Y-m-d H:i:s', strtotime($paymentDataArray['create_time']));

        $payPalPayment = $this->create($payPalPaymentData);

        return $payPalPayment;
    }

    public function updateFromPayPalResponse($payerId, $payPalPayment, $payment)
    {
        $paymentArray = json_decode($payment, true);

        $payPalPaymentData = [];
        $payPalPaymentData['payment_state'] = $paymentArray['state'];
        $payPalPaymentData['execute_json'] = $payment;
        $payPalPaymentData['payer_id'] = $payerId;
        $payPalPaymentData['payer_email'] = $paymentArray['payer']['payer_info']['email'];
        $payPalPaymentData['sale_id'] = $paymentArray['transactions'][0]['related_resources'][0]['sale']['id'];
        $payPalPaymentData['sale_state'] = $paymentArray['transactions'][0]['related_resources'][0]['sale']['state'];
        $payPalPaymentData['transaction_fee'] = $paymentArray['transactions'][0]['related_resources'][0]['sale']['transaction_fee']['value'];
        $payPalPaymentData['payment_execute_at'] = date('Y-m-d H:i:s', strtotime($paymentArray['create_time']));

        $payPalPayment = $this->update($payPalPayment, $payPalPaymentData);

        return $payPalPayment;
    }

    public function create($data)
    {
        $target = new PayPalPayment();
        foreach ($data as $key => $value) {
            $target[$key] = $value;
        }

        $target->save();

        return $target;
    }

    public function update($target, $data)
    {
        foreach ($data as $key => $value) {
            $target[$key] = $value;
        }

        $target->save();

        return $target;
    }
}