<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Enums\PartnerTypeEnum;
use App\Enums\PurchaseOrderStatusEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\Management\PurchaseOrderRequest;
use App\Http\Requests\Management\UpdatePurchaseOrderRequest;
use App\Interfaces\Management\PurchaseOrderServiceInterface;
use App\Models\Partner;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Notifications\NewPurchaseOrder;
use Arr;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Log;
use Number;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PurchaseOrderService implements PurchaseOrderServiceInterface
{
    private bool $isSqlDriver;

    public function __construct()
    {
        $this->isSqlDriver = \in_array(DB::getDriverName(), ['mysql', 'pgsql']);
    }

    /**
     * {@inheritDoc}
     */
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
            ->with(['user:id,name,email', 'user.media', 'supplier:id,name,email'])
            ->when($buyerId, fn ($q, $bId) => $q->where('purchase_orders.user_id', $bId))
            ->when($search, fn ($query, $search) => $query->whereLike('purchase_orders.order_number', "%$search%")->orWhereLike('purchase_orders.notes', "%$search%")
                ->orWhereHas('user', fn ($userQuery) => $userQuery->whereLike('users.name', "%$search%")->orWhereLike('users.email', "%$search%"))
                ->orWhereHas('supplier', fn ($supplierQuery) => $supplierQuery->whereLike('partners.name', "%$search%")))
            ->when($supplierName, fn ($q) => $q->whereHas('supplier', fn ($q2) => $q2->whereIn('partners.name', $supplierName)))
            ->when($purchaserName, fn ($q) => $q->whereHas('user', fn ($q2) => $q2->whereIn('users.name', $purchaserName)))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('purchase_orders.created_at', [$start, $end]))
            ->when($status, fn ($q) => $q->whereIn('purchase_orders.status', $status))
            ->leftJoin((new User)->getTable(), 'purchase_orders.user_id', '=', 'users.id')
            ->leftJoin((new Partner)->getTable(), 'purchase_orders.supplier_id', '=', 'partners.id')
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

        $supplierSearch = $search && str_starts_with($search, 'partner:') ? substr($search, 8) : null;
        $productSearch = $search && str_starts_with($search, 'product:') ? substr($search, 8) : null;

        $suppliers = Partner::query()
            ->select(['id', 'name'])
            ->whereType(PartnerTypeEnum::SUPPLIER->value)
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

    /**
     * {@inheritDoc}
     */
    public function createPurchaseOrder(PurchaseOrderRequest $request): void
    {
        $validated = $request->validated();

        $purchaseOrder = Arr::only($validated, [
            'supplier_id', 'order_date', 'forecast_date', 'status', 'notes',
        ]);

        $items = Arr::get($validated, 'items', []);

        DB::transaction(function () use ($purchaseOrder, $items) {
            $orderNumber = 'PO-'.Str::random(8).str_pad((string) PurchaseOrder::count() + 1, 6, '0', STR_PAD_LEFT);

            $totalCost = array_reduce($items, fn ($carry, $item) => $carry + $item['subtotal_price'], 0);

            $purchaseOrder['total_cost'] = $totalCost;
            $purchaseOrder['order_number'] = $orderNumber;
            $purchaseOrder['user_id'] = Auth::id();

            $po = PurchaseOrder::create($purchaseOrder);
            $po->purchaseOrderItems()->createMany($items);

            User::query()->whereHas('roles', fn ($q) => $q->whereIn('name', [RoleEnum::BUYER->value, RoleEnum::FINANCE->value]))
                ->each(function (User $user) use ($po) {
                    $user->notify(new NewPurchaseOrder($po));
                });
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getSuppliersForSelect(?string $search): CursorPaginator
    {
        $supplierSearch = $search && str_starts_with($search, 'partner:') ? substr($search, 8) : null;

        return Partner::query()
            ->select(['id', 'name'])
            ->whereType(PartnerTypeEnum::SUPPLIER->value)
            ->orderByDesc('id')
            ->whereIsActive(true)
            ->when($supplierSearch, fn ($qr) => $qr->whereLike('name', "%$supplierSearch%")
                ->orWhereLike('email', "%$supplierSearch%")->orWhereLike('identification', "%$supplierSearch%"))
            ->cursorPaginate(10, ['id', 'name'], 'suppliers_cursor')
            ->withQueryString();
    }

    /**
     * {@inheritDoc}
     */
    public function updatePurchaseOrder(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): void
    {
        $validated = $request->validated();
        $purchaseOrder->update($validated);
    }

    /**
     * {@inheritDoc}
     */
    public function generateCsv(LengthAwarePaginator $purchaseOrders): BinaryFileResponse
    {
        $finalFilename = Carbon::now()->format('Y_m_d_H_i_s').'_purchase_orders_export.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$finalFilename\"",
        ];

        $csvFileName = tempnam(sys_get_temp_dir(), 'csv_'.Str::ulid()).'.csv';
        $openFile = fopen($csvFileName, 'w');

        fwrite($openFile, "\xEF\xBB\xBF");

        $delimiter = ';';
        $header = ['ID', 'Supplier Name', 'Total Cost', 'Status', 'Created By', 'Created At', 'Updated At'];

        fputcsv($openFile, $header, $delimiter);

        foreach ($purchaseOrders as $order) {
            $row = [
                $order->id,
                $order->supplier->name,
                Number::currency($order->total_cost),
                PurchaseOrderStatusEnum::tryFrom($order->status->value)?->label() ?? ucfirst($order->status),
                $order->user->name,
                $order->created_at,
                $order->updated_at,
            ];

            fputcsv($openFile, $row, $delimiter);
        }

        fclose($openFile);

        Log::info('Purchase Orders: Generated purchase orders CSV export.', ['action_user_id' => Auth::id()]);

        return response()->download($csvFileName, $finalFilename, $headers)->deleteFileAfterSend(true);
    }
}
