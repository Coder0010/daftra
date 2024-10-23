<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Attach products to the order and calculate the total price.
     */
    public function withProducts($count = 1): OrderFactory
    {
        return $this->afterCreating(function (Order $order) use ($count) {
            // Create products using the factory
            $products = Product::factory($count)->create();

            $totalPrice = 0;
            $totalQuantity = 0;

            foreach ($products as $product) {
                // Get stock quantity and make sure it's available
                $stockQuantity = $this->faker->numberBetween(1, min(5, $product->stock_quantity)); // Stock cannot exceed available

                // Decrease the stock of the product
                $product->decrement('stock_quantity', $stockQuantity);

                // Attach product to order with quantity and price at the time of order
                $order->products()->attach($product->id, [
                    'quantity' => $stockQuantity, // Set the quantity for this product
                    'total_price' => $product->price, // Price at the time of order
                ]);

                // Calculate the total price for the order
                $totalPrice += $product->price * $stockQuantity;
                $totalQuantity += $stockQuantity;
            }

            // Update the order with total quantity and total price
//            $order->update([
//                'quantity' => $totalQuantity,
//                'total_price' => $totalPrice,
//            ]);
        });

    }
}
