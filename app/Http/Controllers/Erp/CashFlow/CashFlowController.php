<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp\CashFlow;

use App\Actions\Erp\CashFlowReport;
use App\Events\ErpDataChanged;
use App\Http\Requests\Erp\DataTableRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

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
}
