<?php
namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function isRegisterConfirmed($user)
    {
        return !empty($user['register_at']) ? true : false;
    }

    public function activeWebsites($user, $theme_id)
    {
        $activeWebsites = $user->themeActiveWebsites->where('id', $theme_id)->all();

        $domains = [];
        foreach ($activeWebsites as $activeWebsite) {
            $domains[] = $activeWebsite->pivot->website_domain;
        }

        return $domains;
    }

    public function isFreeUser($user)
    {
        return $user['membership'] === User::MEMBERSHIP_FREE;
    }

    public function isAdvanceUser($user)
    {
        return $this->isProUser($user) || $this->isLifetimeUser($user);
    }

    public function isBasicUser($user)
    {
        return $user['membership'] === User::MEMBERSHIP_BASIC;
    }

    public function isProUser(User $user)
    {
        return $user['membership'] === User::MEMBERSHIP_PRO;
    }

    public function isLifetimeUser($user)
    {
        return $user['membership'] === User::MEMBERSHIP_LIFETIME;
    }

    public function hasTheme($user, $theme_id)
    {
        $theme = $user->themes()->where('theme_id', $theme_id)->first();

        if(!empty($theme)) {
            return true;
        }

        return false;
    }

    public function saveRegisterInfo($user, $ip = null)
    {
        $now = date('Y-m-d H:i:s');
        $user['register_confirm_code'] = null;
        $user['secret_key'] = self::generateSecretKey();
        $user['register_at'] = $now;
        $user['first_login_at'] = $now;
        $user['last_login_at'] = $now;
        $user['last_login_ip'] = $ip;
        $user->save();
    }

    public function saveLoginInfo($user, $ip = null)
    {
        $now = date('Y-m-d H:i:s');

        if(empty($user['first_login_at'])) {
            $user['first_login_at'] = $now;
        }

        $user['last_login_at'] = $now;
        $user['last_login_ip'] = $ip;
        $user->save();
    }

    public function membershipTo($user, $membership)
    {
        $user['membership'] = $membership;

//        if($membership === self::MEMBERSHIP_PRO) {
//            $now = Carbon::now();
//            $this['pro_from'] = clone $now;
//            $this['pro_to'] = $now->addYear(1);
//        }

        $user->save();
    }

    public function membershipToBasic($user)
    {
        $this->membershipTo($user, User::MEMBERSHIP_BASIC);
    }

    public function membershipToPro($user)
    {
        $this->membershipTo($user, User::MEMBERSHIP_PRO);
    }

    public function membershipToLifetime($user)
    {
        $this->membershipTo($user, User::MEMBERSHIP_LIFETIME);
    }

    public function newUser($data)
    {
        $user = new User();
        $user['name'] = trim($data['name']);
        $user['email'] = trim($data['email']);
        $user['password'] = bcrypt($data['password']);
        $user['register_confirm_code'] = strtolower(str_random(30));
        $user->save();

        return $user;
    }

    public static function generateSecretKey()
    {
        $counter = 3;
        do {
            $secretKey = strtoupper(str_random(30));
            $user = User::where('secret_key', $secretKey)->first();
            if(empty($user)) {
                return $secretKey;
            }
        } while(--$counter > 0);

        return null;
    }
}