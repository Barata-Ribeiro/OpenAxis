<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\PayableRequest;
use App\Http\Requests\QueryRequest;
use App\Models\Payable;
use App\Services\Management\PayableService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

class PayableController extends Controller
{
    public function __construct(private PayableService $payableService) {}

    public function index(QueryRequest $request)
    {
        $payables = $this->getPaginatedPayablesFromRequest($request);

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

    public function store(PayableRequest $request)
    {
        try {
            $this->payableService->storePayable($request);

            Log::info('Payable: Successfully stored new payable.', ['action_user_id' => Auth::id()]);

            return to_route('erp.payables.index')->with('success', 'Payable created successfully.');
        } catch (Exception $e) {
            Log::error('Payable: Error storing payable.', [
                'action_user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with('error', 'An error occurred while storing the payable. Please try again.');
        }
    }

    public function show(Payable $payable)
    {
        Log::info('Purchase Orders: Accessed view purchase order page', [
            'action_user_id' => Auth::id(),
            'purchase_id' => $payable,
        ]);

        $payable = $this->payableService->getPayableDetails($payable);

        return Inertia::render('erp/payables/show', [
            'payable' => $payable,
        ]);
    }

    public function edit(QueryRequest $request, Payable $payable)
    {
        Log::info('Payable: Accessed payable edit page.', [
            'action_user_id' => Auth::id(),
            'payable_id' => $payable->id,
        ]);

        [$suppliers, $vendors] = $this->payableService->getCreateFormData($request);

        return Inertia::render('erp/payables/edit', [
            'payable' => $payable,
            'suppliers' => Inertia::scroll(fn () => $suppliers),
            'vendors' => Inertia::scroll(fn () => $vendors),
        ]);
    }

    public function update(Payable $payable, PayableRequest $request)
    {
        try {
            $this->payableService->updatePayable($payable, $request);

            Log::info('Payable: Successfully updated payable.', [
                'action_user_id' => Auth::id(),
                'payable_id' => $payable->id,
            ]);

            return to_route('erp.payables.index')->with('success', 'Payable updated successfully.');
        } catch (Exception $e) {
            Log::error('Payable: Error updating payable.', [
                'action_user_id' => Auth::id(),
                'payable_id' => $payable->id,
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the payable. Please try again.');
        }
    }

    public function generateCsv(QueryRequest $request)
    {
        $userId = Auth::id();

        try {
            $payables = $this->getPaginatedPayablesFromRequest($request);

            if ($payables->isEmpty()) {
                return redirect()->back()->with('error', 'No payables found to generate CSV.');
            }

            return $this->payableService->generateCsvExport($payables);
        } catch (Exception $e) {
            Log::error('Payable: Failed to generate CSV export.', ['action_user_id' => $userId, 'error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'An unknown error occurred while generating the CSV export.');
        }
    }

    /**
     * Build and return a LengthAwarePaginator of payables based on the given request.
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
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of Payable models.
     */
    private function getPaginatedPayablesFromRequest(QueryRequest $request)
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

        return $this->payableService->getPaginatedPayables(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );
    }
}
