<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PayPalPayment;
use App\Models\Product;
use App\Models\User;
use App\Repositories\PayPal;
use App\Services\OrderService;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'productId' => 'required|integer',
            'paymentType' => 'required|in:paypal',
        ]);

        $productId = $request->input('productId');
        $paymentType = $request->input('paymentType');

        $user = Auth::user();

        $approvalLink = $this->orderService->create($user, $productId, $paymentType, $request->root());

        return response()->json([
            'approvalLink' => $approvalLink,
        ]);
    }

    public function paySuccess(Request $request, PayPal $payPal, PlanService $planService)
    {
        $this->validate($request, [
            'oid' => 'required',
            'paymentId' => 'required',
            'PayerID' => 'required',
        ]);

        $orderNo = $request->input('oid');
        $paymentId = $request->input('paymentId');
        $payerId = $request->input('PayerID');

        $payPalPayment = PayPalPayment::where('payment_id', $paymentId)->first();
        if(empty($payPalPayment)) {
            return 'error url 1';
        }

        $order = $payPalPayment->order;
        if($order['order_no'] != $orderNo) {
            return 'error url 2';
        }

        if($order['status'] == Order::PAID || $order['status'] == Order::REFUNDED) {
            return 'paid';
        }

        $total = $payPalPayment['amount'];
        $payment = $payPal->executePayment($paymentId, $payerId, $total);

        $order = $this->orderService->paid($payerId, $payPalPayment, $payment);

        $product = $order->product;
        $user = $order->user;
        $data = [
            'membership' => null,
            'period' => null,
            'account' => $user['email'],
            'total' => $order['paid_amount'],
        ];

        if($product['type'] == Product::TYPE_LIFETIME) {
            $planService->doLifetimePlan($user);

            $data['membership'] = User::MEMBERSHIP_LIFETIME;
            $data['period'] = 'Forever';
        } else if($product['type'] == Product::TYPE_PRO) {
            $planService->doProPlan($user);

            $data['membership'] = User::MEMBERSHIP_PRO;
            $data['period'] = '1 Year';
        } else if($product['type'] == Product::TYPE_THEME) {
            $planService->doBasicPlan($user, $product);

            $data['membership'] = User::MEMBERSHIP_BASIC;
            $data['period'] = '1 Year';
            $data['themeName'] = $product->theme['name'];
        }

        return view('dashboard.pay_success', $data);
    }

    public function payFail(Request $request)
    {
        $this->validate($request, [
            'oid' => 'required',
        ]);

        $orderNo = $request->input('oid');
        $order = Order::where('order_no', $orderNo)->first();

        if(empty($orderNo)) {
            return 'error url';
        }

        if($order['status'] == Order::CANCELLED) {
            return 'cancelled';
        }

        if($order['status'] != Order::UNPAY) {
            return 'error url2';
        }

        $order['status'] = Order::CANCELLED;
        $order->save();

        return view('dashboard.pay_fail', compact('order'));
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
