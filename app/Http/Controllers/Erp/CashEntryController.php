<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp;

use App\Concerns\CashFlowResponses;
use App\Http\Requests\Erp\CashFlowEntryRequest;
use App\Models\CashEntry;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Inertia\Response;
use InertiaUI\Modal\Modal;

final class CashEntryController extends Controller
{
    use CashFlowResponses;

    public function create(Request $request): Modal|Response
    {
        $expense = $request->route('kind') === CashEntry::TYPE_EXPENSE;
        $choices = ['payment_method_id' => $this->paymentMethods()];

        if ($expense) {
            $choices['expense_category_id'] = ExpenseCategory::query()->orderBy('name')->get(['id', 'name']);
        }

        return $this->modal($request, [
            'title' => $expense ? 'New expense' : 'New manual receipt',
            'kind' => $expense ? CashEntry::TYPE_EXPENSE : CashEntry::TYPE_RECEIPT,
            'mode' => 'create',
            'values' => ['occurred_at' => now()->format('Y-m-d\TH:i')],
            'choices' => $choices,
        ]);
    }

    public function store(CashFlowEntryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $kind = $request->route('kind');

        $this->write(fn () => CashEntry::query()->create([
            ...$data, 'type' => $kind, 'created_by_id' => $request->user()->id,
            'occurred_at' => Carbon::createFromFormat('Y-m-d\TH:i', $data['occurred_at'])->startOfMinute(),
        ]));

        return $this->redirect($request, $kind);
    }

    public function show(Request $request, CashEntry $cashEntry): Modal|Response
    {
        $cashEntry->load(['paymentMethod', 'expenseCategory', 'financialAccount.expenseCategory']);

        return $this->modal($request, [
            'title' => $cashEntry->description, 'kind' => $cashEntry->type, 'mode' => 'show',
            'details' => [
                'Type' => ucfirst($cashEntry->type),
                'Date and time' => ['date' => $cashEntry->occurred_at->toDateTimeString(), 'withTime' => true],
                'Payment method' => $cashEntry->paymentMethod->name,
                'Category' => $cashEntry->expenseCategory->name ?? $cashEntry->financialAccount?->expenseCategory?->name,
                'Amount' => ['cents' => $cashEntry->amount_cents],
                'Fee' => ['cents' => $cashEntry->fee_cents],
                'Notes' => $cashEntry->notes,
            ],
        ]);
    }
}
