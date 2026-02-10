<?php

namespace App\Http\Controllers\Management;

use App\Enums\SalesOrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\SaleOrderRequest;
use App\Http\Requests\Management\UpdateSaleOrderRequest;
use App\Http\Requests\QueryRequest;
use App\Models\PaymentCondition;
use App\Models\SalesOrder;
use App\Services\Management\SalesOrderService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

class SalesOrderController extends Controller
{
    public function __construct(private SalesOrderService $salesOrderService) {}

    public function index(QueryRequest $request)
    {
        $sales = $this->getPaginatedSalesOrdersFromRequest($request);

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

    public function store(SaleOrderRequest $request)
    {
        try {
            $this->salesOrderService->createSalesOrder($request);

            Log::info('Sales Orders: Created new sales order', ['action_user_id' => Auth::id()]);

            return to_route('erp.sales-orders.index')->with('success', 'Sales order created successfully.');
        } catch (Exception $e) {
            Log::error('Sales Orders: Error creating sales order', [
                'action_user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with(['error', 'An error occurred while creating the sales order. Please try again.']);
        }
    }

    public function edit(SalesOrder $salesOrder, QueryRequest $request)
    {
        Log::info('Sales Orders: Accessed edit sales order page', [
            'action_user_id' => Auth::id(),
            'sales_order_id' => $salesOrder->id,
        ]);

        $validated = $request->validated();

        $search = trim($validated['search'] ?? '');

        [$clients, $vendors] = $this->salesOrderService->getEditDataForSelect($search);

        $paymentConditions = PaymentCondition::whereIsActive(true)
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('erp/sales/edit', [
            'saleOrder' => $salesOrder->load('user:id,name,email'),
            'clients' => Inertia::scroll(fn () => $clients),
            'vendors' => Inertia::scroll(fn () => $vendors),
            'paymentConditions' => $paymentConditions,
        ]);
    }

    public function update(UpdateSaleOrderRequest $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== SalesOrderStatusEnum::PENDING) {
            Log::warning('Sales Orders: Attempted to update a non-pending sales order', [
                'sales_order_id' => $salesOrder->id,
                'action_user_id' => Auth::id(),
            ]);

            return to_route('erp.sales-orders.index')->with(['error' => 'Only pending sales orders can be updated.']);
        }

        try {
            $this->salesOrderService->updateSalesOrder($request, $salesOrder);

            Log::info('Sales Orders: Updated sales order', [
                'sales_order_id' => $salesOrder->id,
                'action_user_id' => Auth::id(),
            ]);

            return to_route('erp.sales-orders.index')->with('success', 'Sales order updated successfully.');
        } catch (Exception $e) {
            Log::error('Sales Orders: Error updating sales order', [
                'sales_order_id' => $salesOrder->id,
                'action_user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with(['error', 'An error occurred while updating the sales order. Please try again.']);
        }
    }

    public function generateCsv(QueryRequest $request)
    {
        $userId = Auth::id();

        try {
            $salesOrders = $this->getPaginatedSalesOrdersFromRequest($request);

            if ($salesOrders->isEmpty()) {
                return back()->with('error', 'No sales orders found to generate CSV.');
            }

            return $this->salesOrderService->generateCsv($salesOrders);
        } catch (Exception $e) {
            Log::error('Sales Orders: Error generating CSV', [
                'action_user_id' => $userId,
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'An unknown error occurred while generating the CSV export.');
        }
    }

    /**
     * Build and return a LengthAwarePaginator of sales orders based on the given request.
     *
     * Applies filtering, searching, sorting and eager-loading options provided by the
     * validated QueryRequest, then paginates the resulting query.
     *
     * Expected request inputs (handled/validated by QueryRequest):
     *  - page / per_page: pagination parameters
     *  - sort: sorting column/direction
     *  - filters: associative array of field => value
     *  - with: relations to eager-load
     *
     * @param  QueryRequest  $request  Validated query parameters for filtering, sorting and pagination.
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of SalesOrder models.
     */
    private function getPaginatedSalesOrdersFromRequest(QueryRequest $request)
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

        return $this->salesOrderService->getPaginatedSalesOrders(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );
    }
}
