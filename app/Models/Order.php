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

    public function paypalPayments()
    {
        return $this->hasMany('App\Models\PaypalPayment');
    }

    public function currentPaypalPayment()
    {
        return $this->belongsTo('App\Models\PaypalPayment', 'payment_no');
    }
}
