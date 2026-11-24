<?php

declare(strict_types=1);

namespace App\Actions\Erp;

use App\Models\CashEntry;
use App\Models\FinancialAccount;
use Carbon\CarbonInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final class CashFlowReport
{
    public function __construct(private readonly CarbonInterface $start, private readonly CarbonInterface $end) {}

    /** @return array<string, int|null> */
    public function summary(bool $canReadCash, bool $canReadPayables): array
    {
        $cash = $canReadCash ? $this->cash()->selectRaw('type, COUNT(*) AS entries, SUM(amount_cents) AS amount, SUM(fee_cents) AS fees')
            ->groupBy('type')->get()->keyBy('type') : collect();
        $receipts = $cash->get(CashEntry::TYPE_RECEIPT);
        $expenses = $cash->get(CashEntry::TYPE_EXPENSE);
        $incoming = (int) ($receipts->amount ?? 0);
        $outgoing = (int) ($expenses->amount ?? 0);

        return [
            'receipts' => $canReadCash ? $incoming : null,
            'expenses' => $canReadCash ? $outgoing : null,
            'balance' => $canReadCash ? $incoming - $outgoing : null,
            'payables' => $canReadPayables ? (int) $this->payables()->sum('outstanding_cents') : null,
            'net_profit' => $canReadCash ? $incoming - $outgoing - (int) ($receipts->fees ?? 0) - (int) ($expenses->fees ?? 0) : null,
            'receipt_count' => $canReadCash ? (int) ($receipts->entries ?? 0) : null,
            'expense_count' => $canReadCash ? (int) ($expenses->entries ?? 0) : null,
        ];
    }

    /**
     * @param  'payables'|'receipts'|'expenses'  $tab
     * @return array<string, mixed>
     */
    public function table(string $tab, TableQuery $table): array
    {
        $payables = $tab === 'payables';
        $query = $payables ? $this->payables() : $this->entries($tab);
        $fields = ['description' => 'text', 'amount' => 'number'];

        $fields += match ($tab) {
            'payables' => ['category' => 'text', 'due_on' => 'date', 'status' => 'select'],
            'receipts' => ['occurred_at' => 'date', 'type' => 'select', 'payment' => 'text'],
            'expenses' => ['occurred_at' => 'date', 'category' => 'text'],
        };
        $searchable = array_keys(array_filter($fields, fn (string $type): bool => $type === 'text'));
        $page = $table->paginate($query, $fields, $searchable, $payables ? 'due_on' : 'occurred_at', ! $payables);

        return $table->response($page);
    }

    private function cash(): Builder
    {
        return CashEntry::query()->toBase()->whereBetween('cash_entries.occurred_at', [
            $this->start->startOfDay(), $this->end->endOfDay(),
        ]);
    }

    private function entries(string $tab): Builder
    {
        $entries = $this->cash()
            ->where('cash_entries.type', $tab === 'receipts' ? CashEntry::TYPE_RECEIPT : CashEntry::TYPE_EXPENSE)
            ->join('payment_methods', 'payment_methods.id', '=', 'cash_entries.payment_method_id')
            ->leftJoin('financial_accounts', 'financial_accounts.id', '=', 'cash_entries.financial_account_id')
            ->leftJoin('expense_categories', fn ($join) => $join->on(
                'expense_categories.id', '=', DB::raw('COALESCE(cash_entries.expense_category_id, financial_accounts.expense_category_id)')
            ))
            ->select('cash_entries.id', 'cash_entries.description', 'cash_entries.occurred_at', 'payment_methods.name as payment', 'expense_categories.name as category')
            ->selectRaw('cash_entries.amount_cents / 100.0 AS amount')
            ->selectRaw("CASE WHEN cash_entries.financial_account_id IS NULL THEN 'manual' ELSE 'settlement' END AS type");

        return DB::query()->fromSub($entries, 'entries');
    }

    private function payables(): Builder
    {
        $settlements = CashEntry::query()->toBase()->select('financial_account_id')
            ->selectRaw('SUM(amount_cents) AS settled')
            ->whereNotNull('financial_account_id')->groupBy('financial_account_id');
        $accounts = FinancialAccount::query()->toBase()
            ->where('financial_accounts.type', FinancialAccount::TYPE_PAYABLE)
            ->where('financial_accounts.due_on', '>=', $this->start->toDateString())
            ->where('financial_accounts.due_on', '<', $this->end->copy()->addDay()->toDateString())
            ->join('expense_categories', 'expense_categories.id', '=', 'financial_accounts.expense_category_id')
            ->leftJoinSub($settlements, 'settlements', 'settlements.financial_account_id', 'financial_accounts.id')
            ->select('financial_accounts.id', 'financial_accounts.description', 'expense_categories.name as category')
            ->selectRaw('DATE(financial_accounts.due_on) AS due_on, financial_accounts.amount_cents / 100.0 AS amount')
            ->selectRaw('CASE WHEN cancelled_at IS NOT NULL THEN 0 ELSE financial_accounts.amount_cents - COALESCE(settled, 0) END AS outstanding_cents')
            ->selectRaw("CASE
                WHEN cancelled_at IS NOT NULL THEN 'cancelled'
                WHEN COALESCE(settled, 0) >= financial_accounts.amount_cents THEN 'paid'
                WHEN DATE(financial_accounts.due_on) < ? THEN 'overdue'
                WHEN COALESCE(settled, 0) > 0 THEN 'partially_paid'
                ELSE 'pending' END AS status", [today()->toDateString()]);

        return DB::query()->fromSub($accounts, 'payables');
    }
}
