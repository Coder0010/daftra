<?php

namespace App\Http\Services;

use App\Models\Product;

class ProductService
{

    public function store(array $data): Product
    {
        return Product::create($data);
    }

    public function update(array $data, Product $product): bool
    {
        return $product->update($data);
    }
}
