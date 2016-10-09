<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaypalPayment;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Paypal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function create(Request $request, Paypal $paypal)
    {
        $productId = $request->input('product_id');
        $paymentType = $request->input('payment_type');

        $product = Product::findOrFail($productId);

        $user = Auth::user();
        $userId = $user['id'];
        $orderNo = str_random(15);
        $productName = $product['name'];
        $amount = $product['price'];

        $payment = $paypal->createPaymentUsingPayPal($request->root(), $productName, $orderNo, $amount);
        $paymentArray = json_decode($payment, true);

        $order = new Order();
        $order['user_id'] = $userId;
        $order['product_id'] = $productId;
        $order['order_no'] = $orderNo;
        $order['payment_type'] = $paymentType;
        $order['price'] = $amount;
        $order['pay_amount'] = $amount;
        $order['status'] = Order::UNPAY;
        $order->save();

        $paypalPayment = new PaypalPayment();
        $paypalPayment['payment_id'] = $paymentArray['id'];
        $paypalPayment['payment_method'] = $paymentArray['payer']['payment_method'];
        $paypalPayment['intent'] = $paymentArray['intent'];
        $paypalPayment['amount'] = $paymentArray['transactions'][0]['amount']['total'];
        $paypalPayment['currency'] = $paymentArray['transactions'][0]['amount']['currency'];
        $paypalPayment['payment_state'] = $paymentArray['state'];
        $paypalPayment['create_json'] = $payment;
        $paypalPayment['approval_url'] = $paymentArray['links'][1]['href'];
        $paypalPayment['payment_create_at'] = date('Y-m-d H:i:s', strtotime($paymentArray['create_time']));

        $order->paypalPayments()->save($paypalPayment);
        $order->currentPaypalPayment()->associate($paypalPayment);
        $order->save();

        return redirect($payment->getApprovalLink());
    }

    public function confirm(Request $request, Paypal $paypal)
    {
        $success = $request->input('success');
        $paymentId = $request->input('paymentId');
        $payerId = $request->input('PayerID');

        if($success == 'true') {
            $paypalPayment = PaypalPayment::where('payment_id', $paymentId)->first();
            $total = $paypalPayment['amount'];
            if(!empty($paypalPayment)) {
                $payment = $paypal->executePayment($paymentId, $payerId, $total);
                $paymentArray = json_decode($payment, true);

                $paypalPayment['payment_state'] = $paymentArray['state'];
                $paypalPayment['execute_json'] = $payment;
                $paypalPayment['payer_id'] = $payerId;
                $paypalPayment['sale_id'] = $paymentArray['transactions'][0]['related_resources'][0]['sale']['id'];
                $paypalPayment['sale_state'] = $paymentArray['transactions'][0]['related_resources'][0]['sale']['state'];
                $paypalPayment['transaction_fee'] = $paymentArray['transactions'][0]['related_resources'][0]['sale']['transaction_fee']['value'];
                $paypalPayment['payment_execute_at'] = date('Y-m-d H:i:s', strtotime($paymentArray['create_time']));
                $paypalPayment->save();

                $order = $paypalPayment->order;
                $order['paid_amount'] = $paymentArray['transactions'][0]['related_resources'][0]['sale']['amount']['total'];
                $order['status'] = Order::PAID;
                $order->save();

                $product = $order->product;
                $user = $order->user;
                if($product['type'] == Product::TYPE_LIFETIME) {
                    $user->membershipTo(User::MEMBERSHIP_LIFETIME);
                } else if($product['type'] == Product::TYPE_PRO) {
                    $user->membershipTo(User::MEMBERSHIP_PRO);
                } else if($product['type'] == Product::TYPE_THEME) {
                    $user->membershipTo(User::MEMBERSHIP_BASIC);
                    $user->themes()->attach($product->theme);
                }

                return redirect('/home');
                return $payment;
            }

            return 'no such payment';
        }

        return 'payment fail';
    }

    public function refund(Request $request, Paypal $paypal)
    {
        $saleId = $request->input('sale_id');
//        $saleId = '1DX802939Y8710632';
        $refundedSale = $paypal->saleRefund($saleId);

        return $refundedSale;
    }

    public function getSale(Request $request, Paypal $paypal)
    {
        $saleId = $request->input('sale_id');
//        $saleId = '8XX420913B841634H';
        $refundedSale = $paypal->getSale($saleId);

        return $refundedSale;
    }

    public function createExperience(Request $request, Paypal $paypal)
    {
        $profile = $paypal->CreateWebProfile();

        return $profile;
    }
}
