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
            'per_page' => ['sometimes', 'integer', 'in:5,10,25,75'],

            'sort_by' => ['sometimes', 'string', 'between:1,50', 'regex:/^[A-Za-z0-9_\.]+$/'],
            'sort_dir' => ['sometimes', 'string', 'in:asc,desc'],

            'search' => ['sometimes', 'string', 'between:1,255'],

            'filters' => [
                'sometimes',
                'string',
                'regex:/^(?:[A-Za-z0-9_]+:[A-Za-z0-9\-]+(?:,[A-Za-z0-9\-]+)*)(?:[;|](?:[A-Za-z0-9_]+:[A-Za-z0-9\-]+(?:,[A-Za-z0-9\-]+)*))*$/',
            ],

            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
        ];
    }
}
