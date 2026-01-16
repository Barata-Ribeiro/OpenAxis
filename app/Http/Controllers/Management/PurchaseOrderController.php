<?php

namespace App\Http\Controllers\Management;

use App\Enums\PurchaseOrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\PurchaseOrderRequest;
use App\Http\Requests\QueryRequest;
use App\Models\PurchaseOrder;
use App\Services\Management\PurchaseOrderService;
use Auth;
use Exception;
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

    public function store(PurchaseOrderRequest $request)
    {
        try {
            $this->purchaseOrderService->createPurchaseOrder($request);

            Log::info('Purchase Orders: Successfully created a new purchase order', ['action_user_id' => Auth::id()]);

            return to_route('erp.purchase-orders.index')->with(['success' => 'A new purchase has been registered successfully.']);
        } catch (Exception $e) {
            Log::error('Purchase Orders: Error creating purchase order', [
                'action_user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with(['error', 'An error occurred while creating the purchase order. Please try again.']);
        }
    }

    public function edit(PurchaseOrder $purchaseOrder, QueryRequest $request)
    {
        if ($purchaseOrder->status !== PurchaseOrderStatusEnum::PENDING) {
            Log::warning('Purchase Orders: Attempted to edit a non-pending purchase order', [
                'purchase_order_id' => $purchaseOrder->id,
                'action_user_id' => Auth::id(),
            ]);

            return to_route('erp.purchase-orders.index')->with(['error' => 'Only pending purchase orders can be edited.']);
        }

        Log::info('Purchase Orders: Accessed edit purchase order page', [
            'purchase_order_id' => $purchaseOrder->id,
            'action_user_id' => Auth::id(),
        ]);

        $validated = $request->validated();

        $search = trim($validated['search'] ?? '');

        $suppliers = $this->purchaseOrderService->getSuppliersForSelect($search);

        return Inertia::render('erp/purchases/edit', [
            'purchaseOrder' => $purchaseOrder->load('supplier', 'user', 'user.media', 'purchaseOrderItems'),
            'suppliers' => Inertia::scroll(fn () => $suppliers),
        ]);
    }
}
