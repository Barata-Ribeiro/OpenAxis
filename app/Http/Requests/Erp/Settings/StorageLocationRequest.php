<?php

declare(strict_types=1);

namespace App\Http\Requests\Erp\Settings;

use App\Models\StorageLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Unique;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\HasFluentRules;

final class StorageLocationRequest extends FormRequest
{
    use HasFluentRules;

    /** @return array<string, FluentRuleContract> */
    public function rules(): array
    {
        return [
            'name' => FluentRule::string()->required()->max(255),
            'code' => FluentRule::string()->required()->max(48)->unique(StorageLocation::class, 'code', fn (Unique $rule) => $rule->ignore($this->route('location'))),
            'is_active' => FluentRule::boolean()->required(),
        ];
    }
}
