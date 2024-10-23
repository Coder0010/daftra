<?php

namespace App\Http\Requests\Api\ProductsRequest;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
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
            'page' => 'integer|min:1',
            'name' => 'string|max:255',
            'min_price' => 'numeric|min:0',
            'max_price' => 'numeric|min:0',
        ];
    }
}