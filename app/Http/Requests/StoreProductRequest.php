<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
    public function rules(): array
    {
        return [
            "name" => ["required", "string"],
            "description" => ["required", "string"],
            "price" => ["required", "integer"],
            "quantity" => ["required", "integer"],
            "status" => ["required", Rule::in(["active","inactive"])],
            "categories_ids.*" => ["required","integer","exists:categories,id"]
        ];
    }
}
