<?php

namespace App\Http\Requests\Api\OrdersRequest;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->order);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'products' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $productIds = array_column($value, 'product_id');
                    if (count($productIds) !== count(array_unique($productIds))) {
                        $fail('The products array contains duplicate product IDs.');
                    }
                },
            ],
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    // Extract the index of the current product from the attribute
                    $index = explode('.', $attribute)[1];
                    $productId = request()->input("products.$index.product_id");

                    $product = Product::find($productId);

                    $order = $this->route('order');

                    // Check if the product is already in the order
                    $existingQuantity = 0;
                    if ($order) {
                        $existingProduct = $order->products()->where('product_id', $productId)->first();
                        $existingQuantity = $existingProduct ? $existingProduct->pivot->quantity : 0;
                    }

                    if (!$product) {
                        $fail("The selected product (ID: $productId) is not available.");
                    } elseif (($value - $existingQuantity) > $product->stock_quantity) {
                        $fail(
                            "The requested quantity ($value) exceeds the available stock of $product->stock_quantity for product ID: $productId."
                        );
                    }
                }
            ],
        ];
    }

}
