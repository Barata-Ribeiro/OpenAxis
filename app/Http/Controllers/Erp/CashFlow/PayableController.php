<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp\CashFlow;

use App\Concerns\Erp\CashFlowResponses;
use App\Http\Requests\Erp\CashFlow\CashFlowEntryRequest;
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
        return $this->modal($request, 'erp/cash-flow/payables/create', $this->form());
    }

    public function store(CashFlowEntryRequest $request): RedirectResponse
    {
        $this->write(fn () => FinancialAccount::query()->create([
            ...$request->validated(), 'type' => FinancialAccount::TYPE_PAYABLE, 'created_by_id' => $request->user()->id,
        ]));

        return $this->redirect($request, 'payable', __('Payable created.'));
    }

    public function show(Request $request, FinancialAccount $financialAccount): Modal|Response
    {
        $this->ensurePayable($financialAccount);
        $financialAccount->load('expenseCategory');

        return $this->modal($request, 'erp/cash-flow/payables/show', [
            'title' => $financialAccount->description,
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

        return $this->modal($request, 'erp/cash-flow/payables/edit', $this->form($financialAccount));
    }

    public function update(CashFlowEntryRequest $request, FinancialAccount $financialAccount): RedirectResponse
    {
        $this->ensurePayable($financialAccount);
        $this->write(fn () => $financialAccount->update($request->validated()));

        return $this->redirect($request, 'payable', __('Payable updated.'));
    }

    public function confirmDestroy(Request $request, FinancialAccount $financialAccount): Modal|Response
    {
        $this->ensurePayable($financialAccount);

        return $this->modal($request, 'erp/cash-flow/payables/confirm-destroy', [
            'title' => 'Cancel '.$financialAccount->description,
            'id' => $financialAccount->id,
        ]);
    }

    public function destroy(Request $request, FinancialAccount $financialAccount): RedirectResponse
    {
        $this->ensurePayable($financialAccount);
        $this->write(fn () => $financialAccount->update(['cancelled_at' => $financialAccount->cancelled_at ?? now()]));

        return $this->redirect($request, 'payable', __('Payable cancelled.'));
    }

    /** @return array<string, mixed> */
    private function form(?FinancialAccount $account = null): array
    {
        return [
            'title' => $account === null ? 'New payable' : 'Edit payable',
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
