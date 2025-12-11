<?php

namespace App\Http\Requests\Product;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class AdjustInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user->hasPermissionTo('supply.edit') || $user->hasRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'movement_type' => ['required', 'in:inbound,outbound,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:100'],
            'reference' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'The product is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'movement_type.required' => 'The movement type is required.',
            'movement_type.in' => 'The selected movement type is invalid.',
        ];
    }
}
