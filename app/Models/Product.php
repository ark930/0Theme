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

    const THEME_PRODUCT_ID = 1;
    const PRO_PRODUCT_ID = 2;
    const LIFETIME_PRODUCT_ID = 3;

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

    public static function getBasicProduct()
    {
        return self::find(self::THEME_PRODUCT_ID);
    }

    public static function getProProduct()
    {
        return self::find(self::PRO_PRODUCT_ID);
    }

    public static function getLifetimeProduct()
    {
        return self::find(self::LIFETIME_PRODUCT_ID);
    }
}
