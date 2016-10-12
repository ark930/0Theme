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
        $basicProduct = Product::getThemeProduct();
        $proProduct = Product::getProProduct();
        $lifetimeProduct = Product::getLifetimeProduct();

        return view('dashboard.plan', compact('basicProduct', 'proProduct', 'lifetimeProduct'));
    }

    public function showPlanDetails(Request $request, $membership)
    {
        $user = Auth::user();

        $data = [
            'membership' => $membership,
            'price' => null,
            'themeName' => null,
            'period' => null,
            'account' => $user['email'],
            'upgrade' => [
                'membership' => null,
                'price' => null,
                'link' => null,
            ],
            'paymentType' => 'paypal',
            'productId' =>null,
        ];

        if(User::MEMBERSHIP_BASIC === strtolower($membership)) {
            $themeName = $request->input('theme');
            $basicProduct = Product::where('name', $themeName)->first();
            $proProduct = Product::getProProduct();
            if(!empty($basicProduct)) {
                $data = array_merge($data, [
                    'price' => $basicProduct['price'],
                    'themeName' => $basicProduct['name'],
                    'period' => '1 Year',
                    'upgrade' => [
                        'membership' => User::MEMBERSHIP_PRO,
                        'price' => $proProduct['price'],
                        'link' => url('/plan/pro'),
                    ],
                    'productId' => $basicProduct['id'],
                ]);
            } else {
                $data = array_merge($data, [
                    'period' => '1 Year',
                    'upgrade' => [
                        'membership' => User::MEMBERSHIP_PRO,
                        'price' => $proProduct['price'],
                        'link' => url('/plan/pro'),
                    ],
                ]);
            }
        } else if(User::MEMBERSHIP_PRO === strtolower($membership)) {
            $proProduct = Product::getProProduct();
            $lifetimeProduct = Product::getLifetimeProduct();
            $data = array_merge($data, [
                'price' => $proProduct['price'],
                'themeName' => null,
                'period' => '1 Year',
                'upgrade' => [
                    'membership' => User::MEMBERSHIP_LIFETIME,
                    'price' => $lifetimeProduct['price'],
                    'link' => url('/plan/lifetime'),
                ],
                'productId' => $proProduct['id'],
            ]);
        } else if(User::MEMBERSHIP_LIFETIME === strtolower($membership)) {
            $lifetimeProduct = Product::getLifetimeProduct();
            $data = array_merge($data, [
                'price' => $lifetimeProduct['price'],
                'themeName' => null,
                'period' => 'Forever',
                'upgrade' => null,
                'productId' => $lifetimeProduct['id'],
            ]);
        } else {
            abort(404);
        }

        return view('dashboard.plan_details', $data);
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

        return view('dashboard.main' , compact('user', 'orders', 'themes'));
    }

    public function theme()
    {
        $products = Product::where('id', '>', 1000)->get();

        return view('theme', compact('products'));
    }
}
