<?php

declare(strict_types=1);

namespace App\Http\Requests\Erp\Settings;

use App\Models\UnitOfMeasure;
use Illuminate\Foundation\Http\FormRequest;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\HasFluentRules;

final class UnitOfMeasureRequest extends FormRequest
{
    use HasFluentRules;

    /** @return array<string, FluentRuleContract> */
    public function rules(): array
    {
        $creating = $this->route('unit') === null;

        return [
            'name' => FluentRule::string()->required()->max(255),
            'code' => $creating ? FluentRule::string()->required()->max(16)->unique(UnitOfMeasure::class) : FluentRule::field()->prohibited(),
            'quantity_decimals' => $creating ? FluentRule::integer()->required()->between(0, 3) : FluentRule::field()->prohibited(),
        ];
    }
}
