<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeVersion extends Model
{
    //

    public function theme()
    {
        return $this->belongsTo('App\Models\Theme');
    }

    public function downloads()
    {
        return $this->hasMany('App\Models\ThemeDownload');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag', 'theme_version_tags', 'theme_version_id', 'tag_id');
    }
}
