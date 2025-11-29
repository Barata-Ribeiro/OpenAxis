<?php

namespace App\Services\Management;

use App\Interfaces\Management\PaymentConditionServiceInterface;
use App\Models\PaymentCondition;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentConditionService implements PaymentConditionServiceInterface
{
    public function getPaginatedPaymentConditions(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $is_active = $filters['is_active'][0] ?? null;

        return PaymentCondition::query()
            ->when($search, fn ($q, $search) => $q->whereLike('code', "%$search%")
                ->orWhereLike('name', "%$search%"))
            ->when($is_active, fn ($q) => $q->where('is_active', filter_var($is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }
}
