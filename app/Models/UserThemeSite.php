<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserThemeSite extends Model
{
    public function userTheme()
    {
        return $this->belongsTo('App\Models\UserTheme');
    }
}
