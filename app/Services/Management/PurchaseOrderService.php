<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Enums\RoleEnum;
use App\Interfaces\Management\PurchaseOrderServiceInterface;
use App\Models\Partner;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PurchaseOrderService implements PurchaseOrderServiceInterface
{
    private bool $isSqlDriver;

    public function __construct()
    {
        $this->isSqlDriver = \in_array(DB::getDriverName(), ['mysql', 'pgsql']);
    }

    public function getPaginatedPurchaseOrders(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $requestingUser = Auth::user();
        $buyerId = $requestingUser->hasRole(RoleEnum::BUYER->value) ? Auth::id() : null;

        $status = $filters['status'] ?? [];
        $supplierName = $filters['supplier_name'] ?? [];
        $purchaserName = $filters['user_name'] ?? [];

        $createdAtRange = $filters['created_at'] ?? [];
        [$start, $end] = Helpers::getDateRange($createdAtRange);

        $sortByStartsWithSupplier = str_starts_with((string) $sortBy, 'supplier_name');
        $sortByStartsWithUser = str_starts_with((string) $sortBy, 'user_name');

        if (! empty($sortBy) && $sortByStartsWithSupplier) {
            $sortBy = str_replace('supplier_name', 'partners.name', $sortBy);
        }

        if (! empty($sortBy) && $sortByStartsWithUser) {
            $sortBy = str_replace('user_name', 'users.name', $sortBy);
        }

        return PurchaseOrder::query()
            ->select('purchase_orders.*')
            ->with(['user:id,name,email', 'user.media', 'supplier:id,name'])
            ->when($buyerId, fn ($q, $bId) => $q->where('purchase_orders.user_id', $bId))
            ->when($search, fn ($query, $search) => $query->whereLike('purchase_orders.order_number', "%$search%")->orWhereLike('purchase_orders.notes', "%$search%")
                ->orWhereHas('user', fn ($userQuery) => $userQuery->whereLike('users.name', "%$search%")->orWhereLike('users.email', "%$search%"))
                ->orWhereHas('supplier', fn ($supplierQuery) => $supplierQuery->whereLike('partners.name', "%$search%")))
            ->when($supplierName, fn ($q) => $q->whereHas('supplier', fn ($q2) => $q2->whereIn('partners.name', $supplierName)))
            ->when($purchaserName, fn ($q) => $q->whereHas('user', fn ($q2) => $q2->whereIn('users.name', $purchaserName)))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($status, fn ($q) => $q->whereIn('purchase_orders.status', $status))
            ->leftJoin((new User)->getTable(), 'purchase_orders.user_id', '=', 'users.id')
            ->leftJoin((new Partner)->getTable(), 'purchase_orders.supplier_id', '=', 'partners.id')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getCreateDataForSelect(?string $search): array
    {
        $isSql = $this->isSqlDriver;

        $supplierSearch = $search && str_starts_with($search, 'partner:') ? substr($search, 8) : null;
        $productSearch = $search && str_starts_with($search, 'product:') ? substr($search, 8) : null;

        $suppliers = Partner::query()
            ->select(['id', 'name'])
            ->whereType('supplier')
            ->orderByDesc('id')
            ->whereIsActive(true)
            ->when($supplierSearch, fn ($qr) => $qr->whereLike('name', "%$supplierSearch%")
                ->orWhereLike('email', "%$supplierSearch%")->orWhereLike('identification', "%$supplierSearch%"))
            ->cursorPaginate(10, ['id', 'name'], 'suppliers_cursor')
            ->withQueryString();

        $products = Product::query()
            ->select(['id', 'name', 'sku', 'description', 'selling_price'])
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
            ->cursorPaginate(10, ['id', 'name', 'sku', 'description', 'selling_price'], 'products_cursor')
            ->withQueryString();

        foreach ($products->items() as $item) {
            $item->makeHidden(['sku', 'description'])->setAppends([]);
        }

        return [$suppliers, $products];
    }
}
