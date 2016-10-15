<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function showPlan()
    {
        $basicProduct = Product::getBasicProduct();
        $proProduct = Product::getProProduct();
        $lifetimeProduct = Product::getLifetimeProduct();

        return view('dashboard.plan', compact('basicProduct', 'proProduct', 'lifetimeProduct'));
    }

    public function showPlanDetails(Request $request, $membership)
    {
        $membership = strtolower($membership);

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

        if(User::MEMBERSHIP_BASIC === $membership) {
            $themeName = $request->input('theme');
            if(!empty($themeName)) {
                $basicProduct = Product::where('name', $themeName)
                    ->where('type', Product::TYPE_THEME)
                    ->first();
            }
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
                $basicProduct = Product::getBasicProduct();
                $data = array_merge($data, [
                    'price' => $basicProduct['price'],
                    'period' => '1 Year',
                    'upgrade' => [
                        'membership' => User::MEMBERSHIP_PRO,
                        'price' => $proProduct['price'],
                        'link' => url('/plan/pro'),
                    ],
                ]);
            }
        } else if(User::MEMBERSHIP_PRO === $membership) {
            $proProduct = Product::getProProduct();
            $lifetimeProduct = Product::getLifetimeProduct();

            $price = $proProduct['price'];
            if($user->isBasicUser()) {
                $basicProduct = Product::getBasicProduct();
                $price -= $basicProduct['price'];
            }

            $data = array_merge($data, [
                'price' => $price,
                'themeName' => null,
                'period' => '1 Year',
                'upgrade' => [
                    'membership' => User::MEMBERSHIP_LIFETIME,
                    'price' => $lifetimeProduct['price'],
                    'link' => url('/plan/lifetime'),
                ],
                'productId' => $proProduct['id'],
            ]);
        } else if(User::MEMBERSHIP_LIFETIME === $membership) {
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
}
