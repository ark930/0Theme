<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    public function currentVersion()
    {
        return $this->belongsTo('App\Models\ThemeVersion', 'current_version_id');
    }

    public function versions()
    {
        return $this->hasMany('App\Models\ThemeVersion');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_themes', 'user_id', 'theme_id')
                    ->withPivot('is_deactivate', 'deactivate_reason')
                    ->withTimestamps();
    }

    public function userActiveWebsites()
    {
        return $this->belongsToMany('App\Models\User', 'user_theme_sites', 'user_id', 'theme_id')
                    ->withPivot('website_domain')
                    ->withTimestamps();
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function tagsCount()
    {
        $tags = $this->tags;

        $typeCount = $tags->where('type', 'theme_type')->count();
        $categoryCount = $tags->where('category', 'theme_category')->count();

        return [
            'type' => $typeCount,
            'category' => $categoryCount,
        ];
    }
}
