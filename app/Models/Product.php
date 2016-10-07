<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function theme()
    {
        return $this->belongsTo('App\Models\Theme');
    }
}
