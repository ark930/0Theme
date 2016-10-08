<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const TYPE_THEME = 'theme';
    const TYPE_PRO = 'pro';
    const TYPE_LIFETIME = 'lifetime';

    const PERIOD_ONE_YEAR = 'one_year';
    const PERIOD_LIFETIME = 'lifetime';

    protected $casts = [
        'for_sale' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function theme()
    {
        return $this->belongsTo('App\Models\Theme');
    }
}
