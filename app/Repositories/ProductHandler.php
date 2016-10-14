<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\User;

class ProductHandler
{

    public function canDiscount(User $user, Product $product)
    {
        if($user->isBasicUser() && $product->isProProduct()) {
            return true;
        }

        return false;
    }

    public function priceWithDiscount($product)
    {
        $price = $product['price'];

        $basicProduct = Product::getBasicProduct();
        $price -= $basicProduct['price'];

        return $price;
    }
}