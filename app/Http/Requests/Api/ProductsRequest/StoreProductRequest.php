<?php

namespace App\Http\Requests\Api\ProductsRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|unique:products|max:255',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|numeric|min:0',
        ];
    }
}
