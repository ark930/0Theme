<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayPalPayment extends Model
{
    protected $table = 'paypal_payments';

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

}
