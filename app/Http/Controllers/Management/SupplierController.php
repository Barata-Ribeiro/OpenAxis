<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\SupplierRequest;
use App\Http\Requests\QueryRequest;
use App\Models\Partner;
use App\Services\Management\SupplierService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

class SupplierController extends Controller
{
    public function __construct(private SupplierService $supplierService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'name', 'email', 'identification', 'is_active', 'created_at', 'updated_at'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $suppliers = $this->supplierService->getPaginatedSuppliers(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        Log::info('Supplier: Accessed supplier list.', ['action_user_id' => Auth::id()]);

        return Inertia::render('erp/suppliers/index', [
            'suppliers' => $suppliers,
        ]);
    }

    public function create()
    {
        Log::info('Supplier: Accessed supplier creation page.', ['action_user_id' => Auth::id()]);

        return Inertia::render('erp/suppliers/create');
    }

    public function store(SupplierRequest $request)
    {
        $userId = Auth::id();

        try {
            Log::info('Supplier: Store method called.', ['action_user_id' => $userId]);

            $this->supplierService->createSupplier($request);

            return to_route('erp.suppliers.index')->with('success', 'Supplier created successfully.');
        } catch (Exception $e) {
            Log::error('Supplier: Error occurred while storing supplier.', [
                'action_user_id' => $userId,
                'error_message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'An error occurred while creating the supplier. Please try again.');
        }
    }

    public function show(Partner $supplier)
    {
        if ($supplier->type === 'client') {
            return to_route('erp.suppliers.index')->with('error', 'The specified partner is not a supplier.');
        }

        Log::info('Supplier: Accessed supplier details page.', [
            'action_user_id' => Auth::id(),
            'supplier_id' => $supplier->id,
        ]);

        return Inertia::render('erp/suppliers/show', [
            'supplier' => $supplier->load('addresses'),
        ]);
    }

    public function edit(Partner $supplier)
    {
        if ($supplier->type === 'client') {
            return to_route('erp.suppliers.index')->with('error', 'The specified partner is not a supplier.');
        }

        Log::info('Supplier: Accessed supplier edit page.', [
            'action_user_id' => Auth::id(),
            'supplier_id' => $supplier->id,
        ]);

        return Inertia::render('erp/suppliers/edit', [
            'supplier' => $supplier->load('addresses'),
        ]);
    }

    public function update(SupplierRequest $request, Partner $supplier)
    {
        $userId = Auth::id();

        try {
            Log::info('Supplier: Update method called.', [
                'action_user_id' => $userId,
                'supplier_id' => $supplier->id,
            ]);

            $this->supplierService->updateSupplier($request, $supplier);

            return to_route('erp.suppliers.index')->with('success', 'Supplier updated successfully.');
        } catch (Exception $e) {
            Log::error('Supplier: Error occurred while updating supplier.', [
                'action_user_id' => $userId,
                'supplier_id' => $supplier->id,
                'error_message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'An error occurred while updating the supplier. Please try again.');
        }
    }

    public function destroy(Partner $supplier)
    {
        $userId = Auth::id();

        try {
            Log::info('Supplier: Destroy method called.', [
                'action_user_id' => $userId,
                'supplier_id' => $supplier->id,
            ]);

            $supplier->delete();

            return to_route('erp.suppliers.index')->with('success', 'Supplier deleted successfully.');
        } catch (Exception $e) {
            Log::error('Supplier: Error occurred while deleting supplier.', [
                'action_user_id' => $userId,
                'supplier_id' => $supplier->id,
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'An error occurred while deleting the supplier. Please try again.');
        }
    }

    public function forceDestroy(Partner $supplier)
    {
        $userId = Auth::id();

        try {
            Log::info('Supplier: Force destroy method called.', [
                'action_user_id' => $userId,
                'supplier_id' => $supplier->id,
            ]);

            $supplier->forceDelete();

            return to_route('erp.suppliers.index')->with('success', 'Supplier permanently deleted successfully.');
        } catch (Exception $e) {
            Log::error('Supplier: Error occurred while permanently deleting supplier.', [
                'action_user_id' => $userId,
                'supplier_id' => $supplier->id,
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'An error occurred while permanently deleting the supplier. Please try again.');
        }
    }
}
