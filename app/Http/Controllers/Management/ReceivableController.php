<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\ReceivableRequest;
use App\Http\Requests\QueryRequest;
use App\Models\Receivable;
use App\Services\Management\ReceivableService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

class ReceivableController extends Controller
{
    public function __construct(private ReceivableService $receivableService) {}

    public function index(QueryRequest $request)
    {
        $receivables = $this->getPaginatedReceivables($request);

        Log::info('Receivable: Accessed receivable list.', ['action_user_id' => Auth::id()]);

        return Inertia::render('erp/receivables/index', [
            'receivables' => $receivables,
        ]);
    }

    public function show(Receivable $receivable)
    {
        Log::info('Receivable: Accessed receivable detail page.', [
            'action_user_id' => Auth::id(),
            'receivable_id' => $receivable->id,
        ]);

        $receivableDetail = $this->receivableService->getReceivableDetail($receivable);

        return Inertia::render('erp/receivables/show', [
            'receivable' => $receivableDetail,
        ]);
    }

    public function create(QueryRequest $request)
    {
        Log::info('Receivable: Accessed receivable creation page.', ['action_user_id' => Auth::id()]);

        $clients = $this->receivableService->getCreateFormData($request);

        return Inertia::render('erp/receivables/create', [
            'clients' => Inertia::scroll(fn () => $clients),
        ]);
    }

    public function edit(Receivable $receivable, QueryRequest $request)
    {
        Log::info('Receivable: Accessed receivable edit page.', [
            'action_user_id' => Auth::id(),
            'receivable_id' => $receivable->id,
        ]);

        [$receivable, $clients] = $this->receivableService->getEditFormData($receivable, $request);

        return Inertia::render('erp/receivables/edit', [
            'receivable' => $receivable,
            'clients' => Inertia::scroll(fn () => $clients),
        ]);
    }

    public function update(Receivable $receivable, ReceivableRequest $request)
    {
        try {
            $this->receivableService->updateReceivable($receivable, $request);

            Log::info('Receivable: Successfully updated receivable.', [
                'action_user_id' => Auth::id(),
                'receivable_id' => $receivable->id,
            ]);

            return to_route('erp.receivables.index')->with('success', 'Receivable updated successfully.');
        } catch (Exception $e) {
            Log::error('Receivable: Error updating receivable.', [
                'action_user_id' => Auth::id(),
                'receivable_id' => $receivable->id,
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the receivable. Please try again.');
        }
    }

    public function generateCsv(QueryRequest $request)
    {
        $userId = Auth::id();

        try {
            $receivables = $this->getPaginatedReceivables($request);

            if ($receivables->isEmpty()) {
                return redirect()->back()->with('error', 'No receivables found to generate CSV.');
            }

            return $this->receivableService->generateCsv($receivables);
        } catch (Exception $e) {
            Log::error('Receivable: Error generating CSV.', [
                'action_user_id' => $userId,
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'An unknown error occurred while generating the CSV export.');
        }
    }

    /**
     * Build and return a LengthAwarePaginator of receivables based on the given request.
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
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of Receivable models.
     */
    private function getPaginatedReceivables(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'code', 'client_name', 'amount', 'due_date', 'status', 'created_at', 'updated_at'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        return $this->receivableService->getPaginatedReceivables(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );
    }
}
