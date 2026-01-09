<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Models\PaymentCondition;
use App\Services\Management\SalesOrderService;
use Auth;
use Inertia\Inertia;
use Log;

class SalesOrderController extends Controller
{
    public function __construct(private SalesOrderService $salesOrderService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'client_name', 'total_cost', 'status', 'vendor_name', 'created_at', 'updated_at'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $sales = $this->salesOrderService->getPaginatedSalesOrders(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        return Inertia::render('erp/sales/index', [
            'sales' => $sales,
        ]);
    }

    public function create(QueryRequest $request)
    {
        Log::info('Sales Orders: Accessed create sales order page', ['action_user_id' => Auth::id()]);

        $validated = $request->validated();

        $search = trim($validated['search'] ?? '');

        [$clients, $vendors, $products] = $this->salesOrderService->getCreateDataForSelect($search);

        $paymentConditions = PaymentCondition::whereIsActive(true)
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('erp/sales/create', [
            'clients' => Inertia::scroll(fn () => $clients),
            'vendors' => Inertia::scroll(fn () => $vendors),
            'paymentConditions' => $paymentConditions,
            'products' => Inertia::scroll(fn () => $products),
        ]);
    }
}
