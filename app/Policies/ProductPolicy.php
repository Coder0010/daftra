<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the product.
     *
     * @param User    $user
     * @param Product $product
     *
     * @return bool
     */
    public function update(User $user, Product $product): bool
    {
        return $product->created_by === $user->id;
    }
}

