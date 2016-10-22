<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Repositories\CommonDate;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, CommonDate;

    const MEMBERSHIP_FREE = 'free';
    const MEMBERSHIP_BASIC = 'basic';
    const MEMBERSHIP_PRO = 'pro';
    const MEMBERSHIP_LIFETIME = 'lifetime';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'register_confirm_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'register_confirm_code', 'secret_key',
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
            ->withPivot('is_deactivate', 'deactivate_reason', 'basic_from', 'basic_to')
            ->withTimestamps();
    }

    public function themeActiveWebsites()
    {
        return $this->belongsToMany('App\Models\Theme', 'user_theme_sites', 'user_id', 'theme_id')
                    ->withPivot('website_domain')
                    ->withTimestamps();
    }

    public function getRegisterAtAttribute($value)
    {
        return $this->formatDate($value, 'M d, Y H:i:s');
    }

    public function getLastLoginAtAttribute($value)
    {
        return $this->formatDate($value, 'M d, Y H:i:s');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($this['email'], $token));
    }
}
