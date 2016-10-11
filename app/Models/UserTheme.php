<?php

namespace App\Models;

use App\Repositories\CommonDate;
use Illuminate\Database\Eloquent\Model;

class UserTheme extends Model
{
    use CommonDate;

    public function getBasicFromAttribute($value)
    {
        return $this->formatDate($value);
    }

    public function getBasicToAttribute($value)
    {
        return $this->formatDate($value);
    }

    public function userThemeSites()
    {
        return $this->hasMany('App\Models\UserThemeSite');
    }
}
