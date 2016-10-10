<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function showPlan()
    {
        $basicProduct = Product::find(1);
        $proProduct = Product::find(2);
        $lifetimeProduct = Product::find(3);

        return view('plan', compact('basicProduct', 'proProduct', 'lifetimeProduct'));
    }

    public function showPlanDetails($membership)
    {
        $user = Auth::user();

        $data = [
            'membership' => $membership,
            'price' => null,
            'theme_name' => null,
            'period' => null,
            'account' => $user['email'],
            'upgrade' => [
                'membership' => null,
                'price' => null,
            ],
            'paymentType' => 'paypal',
            'productId' =>null,
        ];

        if(User::MEMBERSHIP_BASIC === strtolower($membership)) {
            $basicProduct = Product::find(1);
            $proProduct = Product::find(2);
            $data = array_merge($data, [
                'price' => $basicProduct['price'],
                'theme_name' => null,
                'period' => '1 Year',
                'upgrade' => [
                    'membership' => User::MEMBERSHIP_PRO,
                    'price' => $proProduct['price'],
                ],
                'productId' => 1,
            ]);
        } else if(User::MEMBERSHIP_PRO === strtolower($membership)) {
            $proProduct = Product::find(2);
            $lifetimeProduct = Product::find(3);
            $data = array_merge($data, [
                'price' => $proProduct['price'],
                'theme_name' => null,
                'period' => '1 Year',
                'upgrade' => [
                    'membership' => User::MEMBERSHIP_LIFETIME,
                    'price' => $lifetimeProduct['price'],
                ],
                'productId' => 2,
            ]);
        } else if(User::MEMBERSHIP_LIFETIME === strtolower($membership)) {
            $lifetimeProduct = Product::find(3);
            $data = array_merge($data, [
                'price' => $lifetimeProduct['price'],
                'theme_name' => null,
                'period' => 'Unlimited',
                'upgrade' => null,
                'productId' => 3,
            ]);
        } else {
            abort(404);
        }

        return view('plan_details', $data);
    }

    public function dashboard()
    {
        $user = Auth::user();
        $orders = $user->orders;
        $themes = [];
        if($user->isAdvanceUser()) {
            $themes =  Theme::all();
        } else if($user->isBasicUser()) {
            $themes = $user->themes;
        }

        return view('dashboard', compact('user', 'orders', 'themes'));
    }
}
