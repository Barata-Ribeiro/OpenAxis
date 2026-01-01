<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Services\Management\PayableService;
use Auth;
use Inertia\Inertia;
use Log;

class PayableController extends Controller
{
    public function __construct(private PayableService $payableService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'code', 'supplier_name', 'amount', 'due_date', 'status', 'created_at', 'updated_at'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $payables = $this->payableService->getPaginatedPayables(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        Log::info('Payable: Accessed payable list.', ['action_user_id' => Auth::id()]);

        return Inertia::render('erp/payables/index', [
            'payables' => $payables,
        ]);
    }

    public function create(QueryRequest $request)
    {
        Log::info('Payable: Accessed payable creation page.', ['action_user_id' => Auth::id()]);

        [$suppliers, $vendors] = $this->payableService->getCreateFormData($request);

        return Inertia::render('erp/payables/create', [
            'suppliers' => Inertia::scroll(fn () => $suppliers),
            'vendors' => Inertia::scroll(fn () => $vendors),
        ]);
    }
}
