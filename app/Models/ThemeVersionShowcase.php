<?php

namespace App\MOdels;

use Illuminate\Database\Eloquent\Model;

class ThemeVersionShowcase extends Model
{
    public $timestamps = false;

    public function themeVersion()
    {
        return $this->belongsTo('App\Models\ThemeVersion');
    }
}
