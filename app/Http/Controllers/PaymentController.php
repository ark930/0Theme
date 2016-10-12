<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaypalPayment;
use App\Models\Product;
use App\Models\User;
use App\Repositories\PayPal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function create(Request $request, PayPal $paypal)
    {
        $productId = $request->input('product_id');
        $paymentType = $request->input('payment_type');

        $product = Product::find($productId);
        if(empty($product)) {
            return back()->withErrors('Please select a theme');
        }

        $user = Auth::user();

        if($user['membership'] == User::MEMBERSHIP_LIFETIME) {
            return back()->withErrors('You are already a LIFETIME user');
        } else if($user['membership'] == User::MEMBERSHIP_PRO) {
            if($product['type'] != Product::TYPE_LIFETIME) {
                return back()->withErrors('You are already a PRO user');
            }
        }

        $price = $product['price'];
        if($user->isBasicUser()) {
            $basicProduct = Product::getBasicProduct();
            $price -= $basicProduct['price'];
        }

        $userId = $user['id'];
        $orderNo = Order::makeOrderNo();
        $productName = $product['name'];
        $amount = $price;

        $order = new Order();
        $order['user_id'] = $userId;
        $order['product_id'] = $productId;
        $order['order_no'] = $orderNo;
        $order['payment_type'] = $paymentType;
        $order['price'] = $amount;
        $order['pay_amount'] = $amount;
        $order['status'] = Order::UNPAY;
        $order->save();

        $successUrl = sprintf('%s/%s?oid=%s', $request->root(), 'payment/success', $order['id']);
        $failUrl = sprintf('%s/%s?oid=%s', $request->root(), 'payment/fail', $order['id']);

        try {
            $payment = $paypal->createPaymentUsingPayPal($productName, $orderNo, $amount, $successUrl, $failUrl);
        } catch (\Exception $exception) {
            return back()->withErrors('Something wrong happened while paying, please try again.');
        }
        $paymentArray = json_decode($payment, true);

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

    public function paySuccess(Request $request, PayPal $paypal)
    {
        $orderId = $request->input('oid');
        $paymentId = $request->input('paymentId');
        $payerId = $request->input('PayerID');

        $paypalPayment = PaypalPayment::where('payment_id', $paymentId)->first();
        $total = $paypalPayment['amount'];
        if(!empty($paypalPayment)) {
            try {
                $payment = $paypal->executePayment($paymentId, $payerId, $total);
            } catch (\Exception $exception) {
                return back()->withErrors('Something wrong happened while paying, please try again.');
            }
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
            $data = [
                'membership' => null,
                'period' => null,
                'account' => $user['email'],
                'total' => $order['paid_amount'],
            ];
            if($product['type'] == Product::TYPE_LIFETIME) {
                $user->membershipTo(User::MEMBERSHIP_LIFETIME);

                $data['membership'] = User::MEMBERSHIP_LIFETIME;
                $data['period'] = 'Forever';
            } else if($product['type'] == Product::TYPE_PRO) {
                $user->membershipTo(User::MEMBERSHIP_PRO);

                $data['membership'] = User::MEMBERSHIP_PRO;
                $data['period'] = '1 Year';
            } else if($product['type'] == Product::TYPE_THEME) {
                $now = Carbon::now();
                $user->membershipTo(User::MEMBERSHIP_BASIC);
                $user->themes()->attach($product->theme, [
                    'basic_from' => clone $now,
                    'basic_to' => $now->addYear(1),
                ]);

                $data['membership'] = User::MEMBERSHIP_BASIC;
                $data['period'] = '1 Year';
                $data['themeName'] = $product->theme['name'];
            }

            return view('dashboard.pay_success', $data);
        }

        return 'no such payment';
    }

    public function payFail(Request $request)
    {
        $orderId = $request->input('oid');

        return view('dashboard.pay_fail', compact('orderId'));
    }

    public function refund(Request $request, PayPal $paypal)
    {
        $saleId = $request->input('sale_id');
//        $saleId = '1DX802939Y8710632';
        $refundedSale = $paypal->saleRefund($saleId);

        return $refundedSale;
    }

    public function getSale(Request $request, PayPal $paypal)
    {
        $saleId = $request->input('sale_id');
//        $saleId = '8XX420913B841634H';
        $refundedSale = $paypal->getSale($saleId);

        return $refundedSale;
    }

    public function createExperience(Request $request, PayPal $paypal)
    {
        $profile = $paypal->CreateWebProfile();

        return $profile;
    }
}
