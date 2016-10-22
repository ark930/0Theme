<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;

class PlanService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function canHaveBasicPlan(User $user)
    {
        return !$this->userRepository->isLifetimeUser($user);
    }

    public function canHaveProPlan(User $user)
    {
        return !$this->userRepository->isLifetimeUser($user);
    }

    public function canHaveLifetimePlan(User $user)
    {
        return !$this->userRepository->isLifetimeUser($user);
    }

    public function doBasicPlan(User $user, $product)
    {
        if($this->canHaveBasicPlan($user)) {
            if(!$this->userRepository->isAdvanceUser($user)) {
                $this->userRepository->membershipToBasic($user);
            }

            $theme = $product->theme;
            $userTheme = $user->themes->where('id', $theme['id'])->first();

            if(empty($userTheme)) {
                if($this->userRepository->isProUser($user)) {
                    $pro_from = $user['pro_from'];
                    $pro_to = $user['pro_to'];
                    $period = $this->calculatePeriod($pro_from, $pro_to);
                } else {
                    $period = $this->calculatePeriod();
                }
                $user->themes()->attach($theme, [
                    'basic_from' => $period['from'],
                    'basic_to' => $period['to'],
                ]);
            } else {
                $basic_from = $userTheme->pivot['basic_from'];
                $basic_to = $userTheme->pivot['basic_to'];
                if($this->userRepository->isProUser($user) && strtotime($user['pro_to']) > strtotime($basic_to)) {
                    $pro_to = $user['pro_to'];
                    $period = $this->calculatePeriod($basic_from, $pro_to);
                } else {
                    $period = $this->calculatePeriod($basic_from, $basic_to);
                }

                $user->themes()->updateExistingPivot($theme['id'], [
                    'basic_from' => $period['from'],
                    'basic_to' => $period['to'],
                ]);
            }
        }
    }

    public function doProPlan(User $user)
    {
        if($this->canHaveProPlan($user)) {
            if(!$this->userRepository->isLifetimeUser($user)) {
                $this->userRepository->membershipToPro($user);
            }

            $period = $this->calculatePeriod($user['pro_from'], $user['pro_to']);
            $user['pro_from'] = $period['from'];
            $user['pro_to'] = $period['to'];
            $user->save();
        }
    }

    public function doLifetimePlan(User $user)
    {
        if($this->canHaveLifetimePlan($user)) {
            $this->userRepository->membershipToLifetime($user);
        }
    }

    public function timeExpired($stringDate)
    {
        return strtotime($stringDate) < time();
    }

    public function isThemeExpired($theme)
    {
        $basic_to = $theme->pivot['basic_to'];

        return $this->timeExpired($basic_to);
    }

    public function isProExpired($user)
    {
        $pro_to = $user['pro_to'];

        return $this->timeExpired($pro_to);
    }

    public function calculatePeriod($oldFrom = null, $oldTo = null)
    {
        if(is_null($oldTo) || $this->timeExpired($oldTo)) {
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