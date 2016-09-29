<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeDownload extends Model
{
    //

    public function themeVersion()
    {
        return $this->belongsTo('App\Models\ThemeVersion');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
