<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Models\Receivable;
use App\Services\Management\ReceivableService;
use Auth;
use Inertia\Inertia;
use Log;

class ReceivableController extends Controller
{
    public function __construct(private ReceivableService $receivableService) {}

    public function index(QueryRequest $request)
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

        $receivables = $this->receivableService->getPaginatedReceivables(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

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
}
