<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp;

use App\Actions\Erp\InventoryReport;
use App\Events\ErpDataChanged;
use App\Http\Requests\Erp\DataTableRequest;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

final class InventoryController extends Controller
{
    public function __invoke(DataTableRequest $request, InventoryReport $report): Response
    {
        $event = new ErpDataChanged('inventory');

        return Inertia::render('erp/inventory/index', [
            'summary' => Inertia::live($report->summary(...), on: $event),
            'table' => Inertia::live(fn () => $report->table($request->tableQuery()), on: $event),
        ]);
    }
}
