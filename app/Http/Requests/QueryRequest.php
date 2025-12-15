<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QueryRequest extends FormRequest
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
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', app()->environment('testing') ? '' : 'in:5,10,25,75'],

            'sort_by' => ['sometimes', 'string', 'between:1,50', 'regex:/^[A-Za-z0-9_\.]+$/'],
            'sort_dir' => ['sometimes', 'string', 'in:asc,desc'],

            'search' => ['sometimes', 'string', 'between:1,255'],

            'filters' => ['sometimes', 'nullable', 'array'],
        ];
    }

    public function prepareForValidation(): void
    {
        $filters = $this->input('filters');

        // Parse filters from string format "key1:value1,value2,key2:value3,value4..."
        if (\is_string($filters) && preg_match_all('/(?:^|,)\s*(\w+):([^,]*(?:,(?!\s*\w+:)[^,]*)*)/u', $filters, $m, PREG_SET_ORDER)) {
            $filtersArray = [];
            foreach ($m as $match) {
                $key = $match[1];
                $values = array_filter(array_map('trim', explode(',', $match[2])));
                if (! empty($values)) {
                    $filtersArray[$key] = array_values($values);
                }
            }

            $this->merge(['filters' => $filtersArray]);
        }
    }
}
