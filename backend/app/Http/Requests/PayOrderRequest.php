<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PayOrderRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cartItems' => 'required|array|min:1',
            'cartItems.*.product_id' => 'required|exists:products,id',
            'cartItems.*.qty' => 'required|integer|min:1',
            'cartItems.*.price' => 'required|numeric|min:0',
            'cartItems.*.coupon_id' => 'nullable|exists:coupons,id',
            'success_url' => 'required|url',
        ];
    }
}
