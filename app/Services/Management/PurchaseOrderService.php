<?php

namespace App\Services\Management;

use App\Interfaces\Management\PurchaseOrderServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PurchaseOrderService implements PurchaseOrderServiceInterface
{
    public function getPaginatedPurchaseOrders(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator {}
}
