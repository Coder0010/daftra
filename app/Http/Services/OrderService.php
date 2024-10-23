<?php

namespace App\Http\Services;

use App\Events\OrderCreatedEvent;
use App\Models\Order;
use App\Models\Product;

class OrderService
{
    public function show(Order $order)
    {
        return Order::find($order->id);
    }

    public function store(array $data): Order
    {
        $order = Order::create();

        $totalOrderPrice = 0;

        foreach ($data['products'] as $item) {
            $product = Product::find($item['product_id']);

            $totalPrice = $item['quantity'] * $product->price;

            $order->products()->attach($product->id, [
                'quantity' => $item['quantity'],
                'total_price' => $totalPrice,
            ]);

            $product->decrement('stock_quantity', $item['quantity']);

            $totalOrderPrice += $totalPrice;
        }

        event(new OrderCreatedEvent($order));

        $order->update(['total_price' => $totalOrderPrice]);

        return $order;
    }

}
