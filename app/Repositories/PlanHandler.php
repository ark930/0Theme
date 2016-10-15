<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;

class PlanHandler
{
    public static function canHaveBasicPlan(User $user)
    {
        return !$user->isLifetimeUser();
    }

    public static function canHaveProPlan(User $user)
    {
        return !$user->isLifetimeUser();
    }

    public static function canHaveLifetimePlan(User $user)
    {
        return !$user->isLifetimeUser();
    }

    public static function doBasicPlan(User $user, $product)
    {
        if(self::canHaveBasicPlan($user)) {
            if(!$user->isAdvanceUser()) {
                $user->membershipToBasic();
            }

            $theme = $product->theme;
            $userTheme = $user->themes->where('id', $theme['id'])->first();

            if(empty($userTheme)) {
                if($user->isProUser()) {
                    $pro_from = $user['pro_from'];
                    $pro_to = $user['pro_to'];
                    $period = self::calculatePeriod($pro_from, $pro_to);
                } else {
                    $period = self::calculatePeriod();
                }
                $user->themes()->attach($theme, [
                    'basic_from' => $period['from'],
                    'basic_to' => $period['to'],
                ]);
            } else {
                $basic_from = $userTheme->pivot['basic_from'];
                $basic_to = $userTheme->pivot['basic_to'];
                if($user->isProUser() && strtotime($user['pro_to']) > strtotime($basic_to)) {
                    $pro_to = $user['pro_to'];
                    $period = self::calculatePeriod($basic_from, $pro_to);
                } else {
                    $period = self::calculatePeriod($basic_from, $basic_to);
                }

                $user->themes()->updateExistingPivot($theme['id'], [
                    'basic_from' => $period['from'],
                    'basic_to' => $period['to'],
                ]);
            }
        }
    }

    public static function doProPlan(User $user)
    {
        if(self::canHaveProPlan($user)) {
            if(!$user->isLifetimeUser()) {
                $user->membershipToPro();
            }

            $period = self::calculatePeriod($user['pro_from'], $user['pro_to']);
            $user['pro_from'] = $period['from'];
            $user['pro_to'] = $period['to'];
            $user->save();
        }
    }

    public static function doLifetimePlan(User $user)
    {
        if(self::canHaveLifetimePlan($user)) {
            $user->membershipToLifetime();
        }
    }

    public static function timeExpired($stringDate)
    {
        return strtotime($stringDate) < time();
    }

    public static function isThemeExpired($theme)
    {
        $basic_to = $theme->pivot['basic_to'];

        return self::timeExpired($basic_to);
    }

    public static function isProExpired($user)
    {
        $pro_to = $user['pro_to'];

        return self::timeExpired($pro_to);
    }

    public static function calculatePeriod($oldFrom = null, $oldTo = null)
    {
        if(is_null($oldTo) || self::timeExpired($oldTo)) {
            $now = Carbon::now();
            $newFrom = clone $now;
            $newTo = $now->addYear(1);
        } else {
            $dt = Carbon::parse($oldTo);
            $newFrom = $oldFrom;
            $newTo =  $dt->addYear(1);
        }

        return [
            'from' => $newFrom,
            'to' => $newTo,
        ];
    }
}