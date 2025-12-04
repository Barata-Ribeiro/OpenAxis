<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Enums\RoleEnum;
use App\Interfaces\Management\SalesOrderServiceInterface;
use App\Models\Partner;
use App\Models\PaymentCondition;
use App\Models\SalesOrder;
use App\Models\Vendor;
use Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SalesOrderService implements SalesOrderServiceInterface
{
    public function getPaginatedSalesOrders(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $requestingUser = Auth::user();

        $vendorId = $requestingUser->hasRole(RoleEnum::VENDOR->value) ? Vendor::whereUserId($requestingUser->id)->pluck('id') : null;

        $createdAtRange = $filters['created_at'] ?? [];
        [$start, $end] = Helpers::getDateRange($createdAtRange);

        return SalesOrder::query()
            ->select('sales_orders.*')
            ->with('user:id,name,email')
            ->select('partners.name as client_name', 'vendors.name as vendor_name', 'payment_conditions.terms as payment_terms')
            ->when($vendorId, fn ($q, $vId) => $q->whereIn('sales_orders.vendor_id', $vId))
            ->when($search, fn ($query, $search) => $query->whereLike('sales_orders.order_number', "%$search%")
                ->orWhereLike('sales_orders.notes', "%$search%")->orWhereHas('user', fn ($userQuery) => $userQuery->whereLike('name', "%$search%")
                ->orWhereLike('email', "%$search%"))->orWhereLike('partners.name', "%$search%")->orWhereLike('vendors.name', "%$search%")
                ->orWhereLike('payment_conditions.code', "%$search%")->orWhereLike('payment_conditions.name', "%$search%"))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->leftJoin((new Partner)->getTable(), 'sales_orders.client_id', '=', 'partners.id')
            ->leftJoin((new Vendor)->getTable(), 'sales_orders.vendor_id', '=', 'vendors.id')
            ->leftJoin((new PaymentCondition)->getTable(), 'sales_orders.payment_condition_id', '=', 'payment_conditions.id')
            ->paginate($perPage);
    }
}
