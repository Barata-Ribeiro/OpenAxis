<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Enums\RoleEnum;
use App\Interfaces\Management\SalesOrderServiceInterface;
use App\Models\Partner;
use App\Models\PaymentCondition;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\Vendor;
use Auth;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SalesOrderService implements SalesOrderServiceInterface
{
    private bool $isSqlDriver;

    public function __construct()
    {
        $this->isSqlDriver = \in_array(DB::getDriverName(), ['mysql', 'pgsql']);
    }

    /**
     * {@inheritDoc}
     */
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
            ->when($createdAtRange, fn ($q) => $q->whereBetween('sales_orders.created_at', [$start, $end]))
            ->leftJoin((new Partner)->getTable(), 'sales_orders.client_id', '=', 'partners.id')
            ->leftJoin((new Vendor)->getTable(), 'sales_orders.vendor_id', '=', 'vendors.id')
            ->leftJoin((new PaymentCondition)->getTable(), 'sales_orders.payment_condition_id', '=', 'payment_conditions.id')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * {@inheritDoc}
     */
    public function getCreateDataForSelect(?string $search): array
    {
        $isSql = $this->isSqlDriver;

        $clientSearch = $search && str_starts_with($search, 'partner:') ? substr($search, 8) : null;
        $vendorSearch = $search && str_starts_with($search, 'vendor:') ? substr($search, 7) : null;
        $productSearch = $search && str_starts_with($search, 'product:') ? substr($search, 8) : null;

        $clients = Partner::query()
            ->select(['id', 'name'])
            ->whereType('client')
            ->orderByDesc('id')
            ->whereIsActive(true)
            ->when($clientSearch, fn ($qr) => $qr->whereLike('name', "%$clientSearch%")
                ->orWhereLike('email', "%$clientSearch%")->orWhereLike('identification', "%$clientSearch%"))
            ->cursorPaginate(10, ['id', 'name'], 'clients_cursor')
            ->withQueryString();

        $vendors = Vendor::query()
            ->select(['id', 'first_name', 'last_name'])
            ->with(['user:id,name,email', 'user.media'])
            ->orderByDesc('id')
            ->whereIsActive(true)
            ->when($vendorSearch, fn ($qr) => $qr->whereLike('first_name', "%$vendorSearch%")
                ->orWhereLike('last_name', "%$vendorSearch%")->orWhereHas('user', fn ($userQr) => $userQr->whereLike('name', "%$vendorSearch%")
                ->orWhereLike('email', "%$vendorSearch%")))
            ->cursorPaginate(10, ['id', 'first_name', 'last_name'], 'vendors_cursor')
            ->withQueryString();

        $products = Product::query()
            ->select(['id', 'name', 'sku', 'description', 'comission', 'selling_price'])
            ->orderByDesc('id')
            ->whereIsActive(true)
            ->when($productSearch, function ($qr) use ($productSearch, $isSql) {
                if ($isSql) {
                    $booleanQuery = Helpers::buildBooleanQuery($productSearch);
                    $qr->whereFullText(['sku', 'name', 'description'], $booleanQuery, ['mode' => 'boolean']);
                } else {
                    $qr->where(function ($q) use ($productSearch) {
                        $q->whereLike('sku', "%$productSearch%")->orWhereLike('name', "%$productSearch%")
                            ->orWhereLike('description', "%$productSearch%");
                    });
                }
            })
            ->cursorPaginate(10, ['id', 'name', 'sku', 'description', 'comission', 'selling_price'], 'products_cursor')
            ->withQueryString();

        foreach ($products->items() as $item) {
            $item->makeHidden(['sku', 'description'])->setAppends([]);
        }

        return [$clients, $vendors, $products];
    }
}
