<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Services\Management\PurchaseOrderService;
use Auth;
use Inertia\Inertia;
use Log;

class PurchaseOrderController extends Controller
{
    public function __construct(private PurchaseOrderService $purchaseOrderService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'supplier_name', 'total_cost', 'status', 'user_name', 'created_at', 'updated_at'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        Log::info('Purchase Orders: Accessed listing of purchases', ['action_user_id' => Auth::id()]);

        $purchases = $this->purchaseOrderService->getPaginatedPurchaseOrders(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        return Inertia::render('erp/purchases/index', [
            'purchases' => $purchases,
        ]);
    }

    public function create(QueryRequest $request)
    {
        Log::info('Purchase Orders: Accessed create purchase order page', ['action_user_id' => Auth::id()]);

        $validated = $request->validated();

        $search = trim($validated['search'] ?? '');

        [$suppliers, $products] = $this->purchaseOrderService->getCreateDataForSelect($search);

        return Inertia::render('erp/purchases/create', [
            'suppliers' => Inertia::scroll(fn () => $suppliers),
            'products' => Inertia::scroll(fn () => $products),
        ]);
    }
}
