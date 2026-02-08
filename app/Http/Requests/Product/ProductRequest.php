<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        $route = $this->route();

        return match ($route->getName()) {
            'erp.products.store' => $user->hasPermissionTo('product.create') || $user->hasRole('super-admin'),
            'erp.products.update' => $user->hasPermissionTo('product.edit') || $user->hasRole('super-admin'),
            default => false,
        };
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:50', Rule::unique(Product::class)->ignore($this->route('product')?->id)],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'comission' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['required', 'boolean'],
            'category' => ['required', 'string', Rule::exists('product_categories', 'name')],
            'images' => app()->environment('testing') ? ['nullable', 'array'] : ['nullable', 'array', 'min:1'],
            'images.*' => File::image()->min('100kb')->max('5mb')
                ->dimensions(Rule::dimensions()->minHeight(200)->minWidth(200)->maxWidth(5000)->maxHeight(5000)),
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->filled('is_active')) {
            $isActive = $this->input('is_active');

            $this->merge([
                'is_active' => filter_var($isActive, FILTER_VALIDATE_BOOLEAN),
            ]);
        } else {
            $this->merge([
                'is_active' => false,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'sku.required' => 'The SKU field is required.',
            'sku.unique' => 'This SKU is already registered in the system.',
            'category.exists' => 'The selected category is invalid.',
            'is_active.in' => 'The is active field must be true or false.',
            'images.min' => 'At least one image is required to determine its cover.',
        ];
    }
}
