<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use App\Concerns\ProfileValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\HasFluentRules;

final class ProfileUpdateRequest extends FormRequest
{
    use HasFluentRules, ProfileValidationRules;

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (config('fortify.lowercase_usernames') && is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower($this->input('email'))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, FluentRuleContract>
     */
    public function rules(): array
    {
        return $this->profileRules($this->user()->id);
    }
}
