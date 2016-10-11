<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Repositories\CommonDate;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, CommonDate;

    const MEMBERSHIP_FREE = 'free';
    const MEMBERSHIP_BASIC = 'basic';
    const MEMBERSHIP_PRO = 'pro';
    const MEMBERSHIP_LIFETIME = 'lifetime';

    public function getRegisterAtAttribute($value)
    {
        return $this->formatDate($value);
    }

    public function getLastLoginAtAttribute($value)
    {
        return $this->formatDate($value);
    }

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
            ->withPivot('is_deactivate', 'deactivate_reason', 'basic_from', 'basic_to')
            ->withTimestamps();
    }

    public function themeActiveWebsites()
    {
        return $this->belongsToMany('App\Models\Theme', 'user_theme_sites', 'user_id', 'theme_id')
                    ->withPivot('website_domain')
                    ->withTimestamps();
    }

    public function isRegisterConfirmed()
    {
        return !empty($this['register_at']) ? true : false;
    }

    public function isFreeUser()
    {
        return !in_array($this['membership'], [
            self::MEMBERSHIP_BASIC,
            self::MEMBERSHIP_PRO,
            self::MEMBERSHIP_LIFETIME,
        ]);
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

    public function isAdvanceUser()
    {
        if($this['membership'] === self::MEMBERSHIP_PRO) {
            return true;

            if($this['pro_to'] >= time()) {
                return true;
            } else {
//                $this->membershipTo(self::MEMBERSHIP_FREE);
            }
        } else if($this['membership'] === self::MEMBERSHIP_LIFETIME) {
            return true;
        }

        return false;
    }

    public function isBasicUser()
    {
        if($this['membership'] === self::MEMBERSHIP_BASIC) {
            return true;
            if($this['basic_to'] >= time()) {
                return true;
            } else {
//                $this->membershipTo(self::MEMBERSHIP_FREE);
            }
        }

        return false;
    }

    public function hasTheme($theme_id)
    {
        $theme = $this->themes()->where('theme_id', $theme_id)->first();

        if(!empty($theme)) {
            return true;
        }

        return false;
    }

    public function emailConfirmed()
    {
        $this['register_at'] = date('Y-m-d H:i:s');
        $this->save();
    }

    public function saveRegisterInfo($ip = null)
    {
        $now = date('Y-m-d H:i:s');
        $this['secret_key'] = str_random(30);
        $this['register_at'] = $now;
        $this['first_login_at'] = $now;
        $this['last_login_at'] = $now;
        $this['last_login_ip'] = $ip;
        $this->save();
    }

    public function saveLoginInfo($ip = null)
    {
        $now = date('Y-m-d H:i:s');

        if(empty($this['first_login_at'])) {
            $this['first_login_at'] = $now;
        }

        $this['last_login_at'] = $now;
        $this['last_login_ip'] = $ip;
        $this->save();
    }

    public function membershipTo($membership)
    {
        $this['membership'] = $membership;

        if($membership == self::MEMBERSHIP_PRO) {
            $now = Carbon::now();
            $this['pro_from'] = clone $now;
            $this['pro_to'] = $now->addYear(1);
        }

        $this->save();
    }

    public static function newUser($data)
    {
        $user = new User();
        $user['name'] = $data['name'];
        $user['email'] = $data['email'];
        $user['password'] = bcrypt($data['password']);
        $user['register_confirm_code'] = strtolower(str_random(30));
        $user->save();

        return $user;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($this['email'], $token));
    }

}
