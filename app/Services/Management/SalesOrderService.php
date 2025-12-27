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

        $status = $filters['status'] ?? [];
        $createdAtRange = $filters['created_at'] ?? [];
        [$start, $end] = Helpers::getDateRange($createdAtRange);

        $sortByStartsWithClient = str_starts_with((string) $sortBy, 'client_name');
        $sortByStartsWithVendor = str_starts_with((string) $sortBy, 'vendor_name');

        if (! empty($sortBy) && $sortByStartsWithClient) {
            $sortBy = str_replace('client_name', 'partners.name', $sortBy);
        }

        if (! empty($sortBy) && $sortByStartsWithVendor) {
            $sortBy = str_replace('vendor_name', 'vendors.name', $sortBy);
        }

        return SalesOrder::query()
            ->select('sales_orders.*')
            ->with(['user:id,name,email', 'user.media', 'client:id,name', 'vendor:id,name', 'paymentCondition:id,code,name'])
            ->when($vendorId, fn ($q, $vId) => $q->whereIn('sales_orders.vendor_id', $vId))
            ->when($status, fn ($q) => $q->whereIn('sales_orders.status', $status))
            ->when($search, fn ($query, $search) => $query->whereLike('sales_orders.order_number', "%$search%")
                ->orWhereLike('sales_orders.notes', "%$search%")->orWhereHas('user', fn ($userQuery) => $userQuery->whereLike('name', "%$search%")
                ->orWhereLike('email', "%$search%"))->orWhereLike('partners.name', "%$search%")->orWhereLike('vendors.name', "%$search%")
                ->orWhereLike('payment_conditions.code', "%$search%")->orWhereLike('payment_conditions.name', "%$search%"))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->leftJoin((new Partner)->getTable(), 'sales_orders.client_id', '=', 'partners.id')
            ->leftJoin((new Vendor)->getTable(), 'sales_orders.vendor_id', '=', 'vendors.id')
            ->leftJoin((new PaymentCondition)->getTable(), 'sales_orders.payment_condition_id', '=', 'payment_conditions.id')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }
}
