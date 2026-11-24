<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp;

use App\Actions\Erp\CashFlowReport;
use App\Events\ErpDataChanged;
use App\Http\Requests\Erp\CashFlowEntryRequest;
use App\Http\Requests\Erp\DataTableRequest;
use App\Models\CashEntry;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\PaymentMethod;
use Closure;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use InertiaUI\Modal\Modal;

final class CashFlowController extends Controller
{
    public function index(DataTableRequest $request): Response
    {
        $canReadCash = $request->user()->can('cash-entry.index');
        $canReadPayables = $request->user()->can('financial-account.index');
        $availableTabs = [...($canReadCash ? ['summary', 'receipts', 'expenses'] : []), ...($canReadPayables ? ['payables'] : [])];
        $tab = $request->validated('tab', $canReadCash ? 'summary' : 'payables');
        abort_unless(in_array($tab, $availableTabs, true), 403);

        $today = today();
        $period = $request->validated('period', 'today');
        [$start, $end] = match ($period) {
            'week' => [$today->startOfWeek(), $today->endOfWeek()],
            'month' => [$today->startOfMonth(), $today->endOfMonth()],
            'custom' => [Carbon::parse($request->validated('start')), Carbon::parse($request->validated('end'))],
            default => [$today, $today],
        };

        $report = new CashFlowReport($start, $end);
        $event = new ErpDataChanged('cash-flow');

        return Inertia::render('erp/cash-flow/index', [
            'summary' => Inertia::live(fn () => $report->summary($canReadCash, $canReadPayables), on: $event),
            'table' => $tab === 'summary' ? null : Inertia::live(fn () => $report->table($tab, $request->tableQuery()), on: $event),
            'period' => ['preset' => $period, 'start' => $start->toDateString(), 'end' => $end->toDateString(), 'today' => $today->toDateString()],
            'tab' => $tab,
            'tabs' => $availableTabs,
        ]);
    }

    public function create(Request $request): Modal|Response
    {
        $kind = $request->route('kind');
        abort_unless(in_array($kind, ['receipt', 'expense', 'payable'], true), 404);

        return $this->modal($request, $this->form($kind));
    }

    public function store(CashFlowEntryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $kind = $request->route('kind');

        $this->write(function () use ($request, $data, $kind): void {
            if ($kind === 'payable') {
                FinancialAccount::query()->create([...$data, 'type' => FinancialAccount::TYPE_PAYABLE, 'created_by_id' => $request->user()->id]);

                return;
            }

            CashEntry::query()->create([
                ...$data,
                'type' => $kind,
                'created_by_id' => $request->user()->id,
                'occurred_at' => Carbon::createFromFormat('Y-m-d\TH:i', $data['occurred_at'])->startOfMinute(),
            ]);
        });

        return $this->redirect($request, $kind);
    }

    public function showEntry(Request $request, CashEntry $cashEntry): Modal|Response
    {
        $cashEntry->load(['paymentMethod', 'expenseCategory', 'financialAccount.expenseCategory']);

        return $this->modal($request, [
            'title' => $cashEntry->description, 'kind' => $cashEntry->type, 'mode' => 'show',
            'details' => [
                'Type' => ucfirst($cashEntry->type),
                'Date and time' => $cashEntry->occurred_at->toDateTimeString(),
                'Payment method' => $cashEntry->paymentMethod->name,
                'Category' => $cashEntry->expenseCategory->name ?? $cashEntry->financialAccount?->expenseCategory?->name,
                'Amount (cents)' => $cashEntry->amount_cents,
                'Fee (cents)' => $cashEntry->fee_cents,
                'Notes' => $cashEntry->notes,
            ],
        ]);
    }

    public function showPayable(Request $request, FinancialAccount $financialAccount): Modal|Response
    {
        $this->ensurePayable($financialAccount);
        $financialAccount->load('expenseCategory');

        return $this->modal($request, [
            'title' => $financialAccount->description, 'kind' => 'payable', 'mode' => 'show',
            'details' => [
                'Category' => $financialAccount->expenseCategory->name,
                'Amount (cents)' => $financialAccount->amount_cents,
                'Outstanding (cents)' => $financialAccount->outstandingCents(),
                'Due date' => $financialAccount->due_on->toDateString(),
                'Status' => str($financialAccount->status)->replace('_', ' ')->title()->toString(),
                'Contact' => $financialAccount->contact_name,
                'Phone' => $financialAccount->contact_phone,
                'Notes' => $financialAccount->notes,
            ],
        ]);
    }

    public function editPayable(Request $request, FinancialAccount $financialAccount): Modal|Response
    {
        $this->ensurePayable($financialAccount);

        return $this->modal($request, $this->form('payable', $financialAccount));
    }

    public function updatePayable(CashFlowEntryRequest $request, FinancialAccount $financialAccount): RedirectResponse
    {
        $this->ensurePayable($financialAccount);
        $this->write(fn () => $financialAccount->update($request->validated()));

        return $this->redirect($request, 'payable');
    }

    public function cancelPayable(Request $request, FinancialAccount $financialAccount): Modal|Response
    {
        $this->ensurePayable($financialAccount);

        return $this->modal($request, [
            'title' => 'Cancel '.$financialAccount->description,
            'kind' => 'payable', 'mode' => 'archive', 'id' => $financialAccount->id,
        ]);
    }

    public function destroyPayable(Request $request, FinancialAccount $financialAccount): RedirectResponse
    {
        $this->ensurePayable($financialAccount);
        $this->write(fn () => $financialAccount->update(['cancelled_at' => $financialAccount->cancelled_at ?? now()]));

        return $this->redirect($request, 'payable');
    }

    public function confirmPayment(Request $request, FinancialAccount $financialAccount): Modal|Response
    {
        $this->ensurePayable($financialAccount);
        $outstanding = $financialAccount->outstandingCents();
        abort_unless($outstanding > 0, 409, 'This account has no outstanding balance.');

        $form = $this->form('payment', $financialAccount);
        $form['values']['expected_amount_cents'] = $outstanding;

        return $this->modal($request, [
            ...$form,
            'details' => ['Description' => $financialAccount->description, 'Outstanding (cents)' => $outstanding],
        ]);
    }

    public function pay(CashFlowEntryRequest $request, FinancialAccount $financialAccount): RedirectResponse
    {
        $data = $request->validated();

        $this->write(function () use ($financialAccount, $data, $request): void {
            DB::transaction(function () use ($financialAccount, $data, $request): void {
                $account = FinancialAccount::query()->lockForUpdate()->findOrFail($financialAccount->id);
                $this->ensurePayable($account);
                $outstanding = $account->cancelled_at === null ? $account->amount_cents - $account->lockAndGetSettledCents() : 0;

                if ($outstanding <= 0) {
                    throw new DomainException('This account has no outstanding balance.');
                }

                if ($outstanding !== (int) $data['expected_amount_cents']) {
                    throw new DomainException('The outstanding balance changed. Open the confirmation again to review the new amount.');
                }

                $account->cashEntries()->create([
                    'payment_method_id' => $data['payment_method_id'],
                    'notes' => $data['notes'] ?? null,
                    'type' => CashEntry::TYPE_EXPENSE, 'description' => $account->description,
                    'amount_cents' => $outstanding, 'created_by_id' => $request->user()->id,
                    'occurred_at' => Carbon::createFromFormat('Y-m-d\TH:i', $data['occurred_at'])->startOfMinute(),
                ]);
            }, attempts: 3);
        });

        return $this->redirect($request, 'payable');
    }

    private function ensurePayable(FinancialAccount $account): void
    {
        abort_unless($account->type === FinancialAccount::TYPE_PAYABLE, 404);
    }

    private function write(Closure $write): void
    {
        try {
            $write();
        } catch (DomainException $exception) {
            throw ValidationException::withMessages(['record' => $exception->getMessage()]);
        }
    }

    /**
     * @param  'receipt'|'expense'|'payable'|'payment'  $kind
     * @return array<string, mixed>
     */
    private function form(string $kind, ?FinancialAccount $account = null): array
    {
        $choices = [];

        if ($kind !== 'payable') {
            $choices['payment_method_id'] = PaymentMethod::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        }

        if (in_array($kind, ['payable', 'expense'], true)) {
            $choices['expense_category_id'] = ExpenseCategory::query()->withTrashed()
                ->where(fn ($query) => $query->whereNull('deleted_at')->orWhere('id', $account?->expense_category_id))
                ->orderBy('name')->get(['id', 'name']);
        }

        $title = match ($kind) {
            'receipt' => 'New manual receipt',
            'expense' => 'New expense',
            'payable' => $account === null ? 'New payable' : 'Edit payable',
            'payment' => 'Confirm payment',
        };

        return [
            'title' => $title, 'kind' => $kind,
            'mode' => $kind === 'payable' && $account !== null ? 'edit' : 'create',
            'id' => $account?->id,
            'values' => match (true) {
                $kind !== 'payable' => ['occurred_at' => now()->format('Y-m-d\TH:i')],
                $account === null => ['due_on' => today()->toDateString()],
                default => [
                    ...$account->only(['description', 'expense_category_id', 'amount_cents', 'contact_name', 'contact_phone', 'notes']),
                    'due_on' => $account->due_on->toDateString(),
                ],
            },
            'choices' => $choices,
        ];
    }

    /** @param array<string, mixed> $props */
    private function modal(Request $request, array $props): Modal|Response
    {
        return $request->user()->canAny(['cash-entry.index', 'financial-account.index'])
            ? Inertia::modal('erp/form-modal', $props)->baseRoute('erp.cash-flow.index')
            : Inertia::render('erp/form', $props);
    }

    private function redirect(Request $request, string $kind): RedirectResponse
    {
        $canReadCash = $request->user()->can('cash-entry.index');
        $canReadPayables = $request->user()->can('financial-account.index');

        if (! $canReadCash && ! $canReadPayables) {
            return to_route('dashboard');
        }

        $base = route('erp.cash-flow.index');
        $previous = $request->header(Modal::HEADER_BASE_URL) ?? url()->previous();

        if (strtok($previous, '?') === $base) {
            return redirect()->to($previous);
        }

        return to_route('erp.cash-flow.index', ['tab' => match (true) {
            $kind === 'payable' && $canReadPayables, ! $canReadCash => 'payables',
            $kind === 'receipt' => 'receipts',
            default => 'expenses',
        }]);
    }
}
