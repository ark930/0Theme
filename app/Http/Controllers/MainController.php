<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class MainController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
//        $request->session()->set('user', $user);
//        dd($request->session());
        $token = $user->createToken('Token Name')->accessToken;
//dd($token);
        $orders = $user->orders->where('status', Order::PAID)->all();
//        $orders = $user->orders;
        $themes = [];
        if($user->isAdvanceUser()) {
            $themes =  Theme::all();
        } else if($user->isBasicUser()) {
            $themes = $user->themes;
        }

        return response()->view('dashboard.main' , compact('user', 'orders', 'themes'))
            ->withCookie(Cookie::forget('flarum_session'));
    }

    public function theme()
    {
        $products = Product::where('id', '>', 1000)->get();

        return view('theme', compact('products'));
    }
}
