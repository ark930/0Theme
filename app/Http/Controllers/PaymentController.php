<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function paymentCreate(Request $request)
    {
        $this->validate($request, [
            'productId' => 'required|integer',
            'paymentType' => 'required|in:paypal',
        ]);

        $productId = $request->input('productId');
        $paymentType = $request->input('paymentType');

        $user = $request->user();
        $baseUrl = $request->root();
        $approvalLink = $this->orderService->create($user, $productId, $paymentType, $baseUrl);

        return response()->json([
            'approvalLink' => $approvalLink,
        ]);
    }

    public function paymentSuccess(Request $request)
    {
        $this->validate($request, [
            'oid' => 'required',
            'paymentId' => 'required',
            'PayerID' => 'required',
        ]);

        $orderNo = $request->input('oid');
        $paymentId = $request->input('paymentId');
        $payerId = $request->input('PayerID');

        $data = $this->orderService->paid($payerId, $paymentId, $orderNo);

        return view('dashboard.pay_success', $data);
    }

    public function paymentFailure(Request $request)
    {
        $this->validate($request, [
            'oid' => 'required',
        ]);

        $orderNo = $request->input('oid');
        $order = $this->orderService->canceled($orderNo);

        return view('dashboard.pay_fail', compact('order'));
    }
}
