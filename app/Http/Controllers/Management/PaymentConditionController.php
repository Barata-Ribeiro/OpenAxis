<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Services\Management\PaymentConditionService;
use Inertia\Inertia;

class PaymentConditionController extends Controller
{
    public function __construct(private PaymentConditionService $paymentConditionService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'code', 'name', 'days_until_due', 'installments', 'is_active', 'created_at', 'updated_at'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $paymentConditions = $this->paymentConditionService->getPaginatedPaymentConditions(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        return Inertia::render('erp/payment-conditions/index', [
            'paymentConditions' => $paymentConditions,
        ]);
    }
}
