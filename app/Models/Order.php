<?php

namespace App\Models;

use App\Repositories\CommonDate;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use CommonDate;

    const UNPAY = 'unpay';
    const PAID = 'paid';
    const REFUNDED = 'refunded';
    const CANCELLED = 'cancelled';

    public function getCreatedAtAttribute($value)
    {
        return $this->formatDate($value);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function payPalPayments()
    {
        return $this->hasMany('App\Models\PayPalPayment');
    }

    public function currentPayPalPayment()
    {
        return $this->belongsTo('App\Models\PayPalPayment', 'payment_no');
    }

    public static function generateOrderNo()
    {
        $counter = 3;
        $orderNoSize = 4;

        do {
            $orderNo = date('Ymd') . strtoupper(str_random($orderNoSize));
            $order = Order::where('order_no', $orderNo)->first();
            if(empty($order)) {
                return $orderNo;
            }

            $orderNoSize++;
        } while(--$counter > 0);

        return strtoupper(15);
    }

    public function getPaymentLink()
    {
        $product = $this->product;

        $membership = null;
        if($product['type'] == Product::TYPE_THEME) {
            $membership = User::MEMBERSHIP_BASIC;

            return sprintf( '/plan/%s?theme=%s', $membership, $product->theme['name']);
        }

        $membership = $product['type'];
        return sprintf('/plan/%s', $membership);
    }
}
