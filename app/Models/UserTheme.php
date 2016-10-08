<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTheme extends Model
{
    public function userThemeSites()
    {
        return $this->hasMany('App\Models\UserThemeSite');
    }
}
