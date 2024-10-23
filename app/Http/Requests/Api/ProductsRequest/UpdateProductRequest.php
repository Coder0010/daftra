<?php

namespace App\Http\Requests\Api\ProductsRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->product);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|unique:products,' . $this->id . '|max:255',
            'price' => 'required|numeric',
            'stock_quantity' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $product = $this->product;

                    // Check if the stock quantity to decrement is available
                    if ($product && $value < $product->stock_quantity) {
                        $fail("The provided stock quantity ($value) should not be lower than the available stock of [ $product->stock_quantity ].");
                    }
                },
            ],
        ];
    }
}
