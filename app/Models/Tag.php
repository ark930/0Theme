<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //

    public function themes()
    {
        return $this->belongsToMany('App\Models\Theme', 'theme_tags', 'theme_id', 'tag_id');
    }

}
