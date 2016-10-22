<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Theme;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function dashboard(Request $request, UserRepository $userRepository)
    {
        $user = Auth::user();
        $orders = $user->orders->where('status', Order::PAID)->all();
//        $orders = $user->orders;
        $themes = [];
        if($userRepository->isAdvanceUser($user)) {
            $themes =  Theme::all();
        } else if($userRepository->isBasicUser($user)) {
            $themes = $user->themes;
        }

        return response()->view('dashboard.main' , compact('user', 'orders', 'themes'));
    }

    public function theme()
    {
        $products = Product::where('id', '>', 1000)->get();

        return view('theme', compact('products'));
    }
}
