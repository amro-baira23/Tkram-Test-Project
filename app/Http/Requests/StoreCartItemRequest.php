<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            "order_number" => ["required", "integer"],
            "order_items" => ["required", "array"],
            "order_items.*.product_id" => ["required", "integer","exists:products,id"],
            "order_items.*.quantity" => ["required", "integer"],
            "order_items.*.price" => ["required", "integer"],
        ];
    }
}
