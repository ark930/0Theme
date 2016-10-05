<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function downloads()
    {
        return $this->hasMany('App\Models\ThemeDownload');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function themes()
    {
        return $this->belongsToMany('App\Models\Theme', 'user_themes', 'user_id', 'theme_id')
            ->withPivot('theme_key', 'activate_at', 'basic_expire_at', 'is_deactivate', 'deactivate_reason')
            ->withTimestamps();
    }

    public function themeActiveWebsites()
    {
        return $this->belongsToMany('App\Models\Theme', 'user_theme_sites', 'user_id', 'theme_id')
                    ->withPivot('website_domain')
                    ->withTimestamps();
    }

    public function activeWebsites($theme_id)
    {
        $activeWebsites = $this->themeActiveWebsites->where('id', $theme_id)->all();

        $domains = [];
        foreach ($activeWebsites as $activeWebsite) {
            $domains[] = $activeWebsite->pivot->website_domain;
        }

        return $domains;
    }
}
