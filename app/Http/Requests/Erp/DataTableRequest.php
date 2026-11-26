<?php

declare(strict_types=1);

namespace App\Http\Requests\Erp;

use App\Actions\Erp\TableQuery;
use Illuminate\Foundation\Http\FormRequest;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\HasFluentRules;

final class DataTableRequest extends FormRequest
{
    use HasFluentRules;

    /** @return array<string, FluentRuleContract> */
    public function rules(): array
    {
        return [
            'table' => FluentRule::array(['page', 'perPage', 'search', 'filters', 'sorting'])->children([
                'page' => FluentRule::integer()->between(1, 1_000_000),
                'perPage' => FluentRule::integer()->between(1, 100),
                'search' => FluentRule::string()->nullable()->max(200),
                'filters' => FluentRule::list()->max(20)->each(
                    FluentRule::array(['id', 'operator', 'value', 'filterId', 'joinOperator'])->children([
                        'id' => FluentRule::string()->required()->max(64),
                        'operator' => FluentRule::string()->required()->in(TableQuery::operators()),
                        'value' => FluentRule::field()->nullable(),
                        'filterId' => FluentRule::string()->max(64),
                        'joinOperator' => FluentRule::string()->in(['and', 'or']),
                    ]),
                ),
                'sorting' => FluentRule::list()->max(12)->each(
                    FluentRule::array(['id', 'desc'])->children([
                        'id' => FluentRule::string()->required()->distinct()->max(64),
                        'desc' => FluentRule::boolean()->required(),
                    ]),
                ),
            ]),
            'trashed' => FluentRule::string()->in(['only']),
            'tab' => FluentRule::string()->in(['summary', 'receipts', 'expenses', 'payables']),
            'period' => FluentRule::string()->in(['today', 'week', 'month', 'custom']),
            'start' => FluentRule::date()->format('Y-m-d')->requiredIf('period', 'custom'),
            'end' => FluentRule::date()->format('Y-m-d')->requiredIf('period', 'custom')->afterOrEqual('start'),
        ];
    }

    public function tableQuery(): TableQuery
    {
        return new TableQuery($this->validated('table', []));
    }
}
