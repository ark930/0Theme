<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Theme;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
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
