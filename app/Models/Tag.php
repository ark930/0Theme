<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function themeVersions()
    {
        return $this->belongsToMany('App\Models\ThemeVersion', 'theme_version_tags', 'theme_version_id', 'tag_id');
    }

}
