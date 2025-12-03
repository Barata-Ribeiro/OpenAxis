<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Services\Management\SalesOrderService;

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

        $allowedSorts = ['id']; // Todo: define allowed sorts later
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }
    }
}
