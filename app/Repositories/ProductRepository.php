<?php

namespace App\Repositories;


use App\Models\Product;

class ProductRepository
{
    public function create()
    {

    }

    public function get($id)
    {
        return Product::find($id);
    }

    public function isThemeProduct($product)
    {
        if (Product::THEME_PRODUCT_ID == $product['id']) {
            return true;
        }

        return false;
    }
}