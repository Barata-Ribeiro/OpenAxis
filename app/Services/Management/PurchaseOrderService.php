<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Enums\RoleEnum;
use App\Interfaces\Management\PurchaseOrderServiceInterface;
use App\Models\Partner;
use App\Models\PurchaseOrder;
use Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PurchaseOrderService implements PurchaseOrderServiceInterface
{
    public function getPaginatedPurchaseOrders(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $requestingUser = Auth::user();

        $buyerId = $requestingUser->hasRole(RoleEnum::BUYER->value) ? Auth::id() : null;

        $status = $filters['status'] ?? [];
        $createdAtRange = $filters['created_at'] ?? [];
        [$start, $end] = Helpers::getDateRange($createdAtRange);

        return PurchaseOrder::query()
            ->select('purchase_orders.*')
            ->with('user:id,name,email')
            ->when($buyerId, fn ($q, $bId) => $q->where('purchase_orders.user_id', $bId))
            ->when($search, fn ($query, $search) => $query->whereLike('purchase_orders.order_number', "%$search%")
                ->orWhereLike('purchase_orders.notes', "%$search%")->orWhereHas('user', fn ($userQuery) => $userQuery->whereLike('name', "%$search%")
                ->orWhereLike('email', "%$search%"))->orWhereLike('partners.name', "%$search%"))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($status, fn ($q) => $q->whereIn('purchase_orders.status', $status))
            ->leftJoin((new Partner)->getTable(), 'purchase_orders.supplier_id', '=', 'partners.id')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }
}
