<?php

declare(strict_types=1);

namespace App\Http\Requests\Erp\Settings;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Exists;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\HasFluentRules;

final class CategoryRequest extends FormRequest
{
    use HasFluentRules;

    /** @return array<string, FluentRuleContract> */
    public function rules(): array
    {
        return [
            'name' => FluentRule::string()->required()->max(255),
            'parent_id' => FluentRule::integer()->nullable()->exists(Category::class, 'id', fn (Exists $rule) => $rule->withoutTrashed()),
        ];
    }
}
