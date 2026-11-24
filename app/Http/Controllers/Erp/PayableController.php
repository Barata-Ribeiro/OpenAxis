<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp;

use App\Concerns\CashFlowResponses;
use App\Http\Requests\Erp\CashFlowEntryRequest;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Response;
use InertiaUI\Modal\Modal;

final class PayableController extends Controller
{
    use CashFlowResponses;

    public function create(Request $request): Modal|Response
    {
        return $this->modal($request, $this->form());
    }

    public function store(CashFlowEntryRequest $request): RedirectResponse
    {
        $this->write(fn () => FinancialAccount::query()->create([
            ...$request->validated(), 'type' => FinancialAccount::TYPE_PAYABLE, 'created_by_id' => $request->user()->id,
        ]));

        return $this->redirect($request, 'payable');
    }

    public function show(Request $request, FinancialAccount $financialAccount): Modal|Response
    {
        $this->ensurePayable($financialAccount);
        $financialAccount->load('expenseCategory');

        return $this->modal($request, [
            'title' => $financialAccount->description, 'kind' => 'payable', 'mode' => 'show',
            'details' => [
                'Category' => $financialAccount->expenseCategory->name,
                'Amount' => ['cents' => $financialAccount->amount_cents],
                'Outstanding' => ['cents' => $financialAccount->outstandingCents()],
                'Due date' => ['date' => $financialAccount->due_on->toDateString()],
                'Status' => str($financialAccount->status)->replace('_', ' ')->title()->toString(),
                'Contact' => $financialAccount->contact_name,
                'Phone' => $financialAccount->contact_phone,
                'Notes' => $financialAccount->notes,
            ],
        ]);
    }

    public function edit(Request $request, FinancialAccount $financialAccount): Modal|Response
    {
        $this->ensurePayable($financialAccount);

        return $this->modal($request, $this->form($financialAccount));
    }

    public function update(CashFlowEntryRequest $request, FinancialAccount $financialAccount): RedirectResponse
    {
        $this->ensurePayable($financialAccount);
        $this->write(fn () => $financialAccount->update($request->validated()));

        return $this->redirect($request, 'payable');
    }

    public function confirmDestroy(Request $request, FinancialAccount $financialAccount): Modal|Response
    {
        $this->ensurePayable($financialAccount);

        return $this->modal($request, [
            'title' => 'Cancel '.$financialAccount->description,
            'kind' => 'payable', 'mode' => 'archive', 'id' => $financialAccount->id,
        ]);
    }

    public function destroy(Request $request, FinancialAccount $financialAccount): RedirectResponse
    {
        $this->ensurePayable($financialAccount);
        $this->write(fn () => $financialAccount->update(['cancelled_at' => $financialAccount->cancelled_at ?? now()]));

        return $this->redirect($request, 'payable');
    }

    /** @return array<string, mixed> */
    private function form(?FinancialAccount $account = null): array
    {
        return [
            'title' => $account === null ? 'New payable' : 'Edit payable',
            'kind' => 'payable',
            'mode' => $account === null ? 'create' : 'edit',
            'id' => $account?->id,
            'values' => $account === null ? ['due_on' => today()->toDateString()] : [
                ...$account->only(['description', 'expense_category_id', 'amount_cents', 'contact_name', 'contact_phone', 'notes']),
                'due_on' => $account->due_on->toDateString(),
            ],
            'choices' => [
                'expense_category_id' => ExpenseCategory::query()->withTrashed()
                    ->where(fn ($query) => $query->whereNull('deleted_at')->orWhere('id', $account?->expense_category_id))
                    ->orderBy('name')->get(['id', 'name']),
            ],
        ];
    }
}
