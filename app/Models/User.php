<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const MEMBERSHIP_FREE = 'membership_free';
    const MEMBERSHIP_BASIC = 'membership_basic';
    const MEMBERSHIP_PRO = 'membership_pro';
    const MEMBERSHIP_LIFETIME = 'membership_lifetime';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'email_confirm_code',
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
            ->withPivot('is_deactivate', 'deactivate_reason')
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
