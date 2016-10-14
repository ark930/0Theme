<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaypalPayment;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderHandler;
use App\Repositories\PayPal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function create(Request $request, PayPal $payPal)
    {
        $this->validate($request, [
            'product_id' => 'required|integer',
            'payment_type' => 'required|in:paypal',
        ]);

        $productId = $request->input('product_id');
        $paymentType = $request->input('payment_type');

        $product = Product::find($productId);
        if(empty($product)) {
            if(Product::THEME_PRODUCT_ID == $product['id']) {
                return back()->withErrors('Please select a theme');
            } else {
                return back()->withErrors('Illegal parameters');
            }
        }

        $user = Auth::user();
        if($user['membership'] == User::MEMBERSHIP_LIFETIME) {
            return back()->withErrors('You are already a LIFETIME user');
        } else if($user['membership'] == User::MEMBERSHIP_PRO) {
            if($product['type'] != Product::TYPE_LIFETIME) {
                return back()->withErrors('You are already a PRO user');
            }
        }

        $orderHandler = new OrderHandler();
        $order = $orderHandler->create($user, $product, $paymentType);

        $successUrl = sprintf('%s/%s?oid=%s', $request->root(), 'payment/success', $order['id']);
        $failUrl = sprintf('%s/%s?oid=%s', $request->root(), 'payment/fail', $order['id']);

        try {
            $payment = $payPal->createPaymentUsingPayPal($product['name'], $order['order_no'], $order['pay_amount'], $successUrl, $failUrl);

            $orderHandler->savePayPalPayment($order, $payment);

            return redirect($payment->getApprovalLink());
        } catch (\Exception $exception) {
            return back()->withErrors('Something wrong happened while paying, please try again.');
        }
    }

    public function paySuccess(Request $request, PayPal $payPal)
    {
        $orderId = $request->input('oid');
        $paymentId = $request->input('paymentId');
        $payerId = $request->input('PayerID');

        $payPalPayment = PaypalPayment::where('payment_id', $paymentId)->first();
        $total = $payPalPayment['amount'];
        if(!empty($payPalPayment)) {
            try {
                $payment = $payPal->executePayment($paymentId, $payerId, $total);
            } catch (\Exception $exception) {
                return back()->withErrors('Something wrong happened while paying, please try again.');
            }
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

    public function refund(Request $request, PayPal $payPal)
    {
        $saleId = $request->input('sale_id');
//        $saleId = '1DX802939Y8710632';
        $refundedSale = $payPal->saleRefund($saleId);

        return $refundedSale;
    }

    public function getSale(Request $request, PayPal $payPal)
    {
        $saleId = $request->input('sale_id');
//        $saleId = '8XX420913B841634H';
        $refundedSale = $payPal->getSale($saleId);

        return $refundedSale;
    }

    public function createExperience(Request $request, PayPal $payPal)
    {
        $profile = $payPal->CreateWebProfile();

        return $profile;
    }
}
