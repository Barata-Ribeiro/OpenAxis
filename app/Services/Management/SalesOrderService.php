<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Enums\PartnerTypeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\Management\SaleOrderRequest;
use App\Interfaces\Management\SalesOrderServiceInterface;
use App\Models\Partner;
use App\Models\Payable;
use App\Models\PaymentCondition;
use App\Models\Product;
use App\Models\Receivable;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\NewSalesOrder;
use Arr;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Str;

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
            ->with(['user:id,name,email', 'user.media', 'client:id,name', 'vendor:id,first_name,last_name', 'paymentCondition:id,code,name'])
            ->when($vendorId, fn ($q, $vId) => $q->whereIn('sales_orders.vendor_id', $vId))
            ->when($status, fn ($q) => $q->whereIn('sales_orders.status', $status))
            ->when($search, fn ($query, $search) => $query->whereLike('sales_orders.order_number', "%$search%")
                ->orWhereLike('sales_orders.notes', "%$search%")->orWhereHas('user', fn ($userQuery) => $userQuery->whereLike('name', "%$search%")
                ->orWhereLike('email', "%$search%"))->orWhereLike('partners.name', "%$search%")->orWhereLike('vendors.first_name', "%$search%")
                ->orWhereLike('vendors.last_name', "%$search%")->orWhereRaw("CONCAT(vendors.first_name, ' ', vendors.last_name) LIKE ?", ["%$search%"])
                ->orWhereHas('vendor.user', fn ($vendorUserQuery) => $vendorUserQuery->whereLike('name', "%$search%")
                    ->orWhereLike('email', "%$search%"))->orWhereLike('payment_conditions.code', "%$search%")->orWhereLike('payment_conditions.name', "%$search%"))
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
            ->whereType(PartnerTypeEnum::CLIENT->value)
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
            ->whereRaw('current_stock > 0')
            ->whereIsActive(true)
            ->orderByDesc('id')
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

    /**
     * {@inheritDoc}
     */
    public function createSalesOrder(SaleOrderRequest $request): void
    {
        $validated = $request->validated();

        $createdBy = Auth::id();

        $saleOrder = Arr::only($validated, [
            'client_id', 'vendor_id', 'order_date', 'delivery_date', 'payment_condition_id', 'status', 'payment_method', 'notes',
        ]);

        $items = Arr::get($validated, 'items', []);

        $quantities = collect($items)
            ->groupBy('product_id')
            ->map(fn ($group) => $group->sum('quantity'));

        $statusPayable = $validated['update_payables'] ?? false;
        $statusReceivable = $validated['update_receivables'] ?? false;

        DB::transaction(function () use ($saleOrder, $items, $quantities, $createdBy, $statusReceivable, $statusPayable) {
            $orderNumber = 'SO-'.Str::random(8).str_pad((string) SalesOrder::count() + 1, 6, '0', STR_PAD_LEFT);

            $totalCost = array_reduce($items, fn ($carry, $item) => $carry + $item['subtotal_price'], 0);
            $totalComission = array_reduce($items, fn ($carry, $item) => $carry + $item['commission_item'], 0);
            $productValue = array_reduce($items, fn ($carry, $item) => $carry + ($item['quantity'] * $item['unit_price']), 0);

            $saleOrder['total_cost'] = $totalCost;
            $saleOrder['total_commission'] = $totalComission;
            $saleOrder['product_value'] = $productValue;
            $saleOrder['order_number'] = $orderNumber;
            $saleOrder['user_id'] = $createdBy;

            // Create Sales Order and its items
            $so = SalesOrder::create($saleOrder);
            $so->salesOrderItems()->createMany($items);

            // Handle product stock decrementation and stock movements
            foreach ($quantities as $productId => $qty) {
                Product::where('id', $productId)->decrement('current_stock', (int) $qty);
                StockMovement::create([
                    'product_id' => $productId,
                    'user_id' => $createdBy,
                    'movement_type' => 'outbound',
                    'quantity' => (int) $qty,
                    'reason' => "Sales Order Created: $orderNumber",
                    'reference' => $orderNumber,
                ]);
            }

            // Handle payables/receivables if needed
            $installments = 1;
            $daysUntilDue = 30;
            if (! empty($saleOrder['payment_condition_id'])) {
                $paymentCondition = PaymentCondition::query()
                    ->whereKey($saleOrder['payment_condition_id'])
                    ->whereIsActive(true)
                    ->first();

                if ($paymentCondition !== null) {
                    $installments = $paymentCondition->installments;
                    $daysUntilDue = $paymentCondition->days_until_due;
                }
            }

            $receivableCount = Receivable::count();
            $payableCount = Payable::count() + 1;
            $yearNow = Carbon::now()->year;

            foreach (range(1, $installments) as $installment) {
                $receivableCount++;
                $code = 'RCV-'.$yearNow.'-'.str_pad((string) $receivableCount, 6, '0', STR_PAD_LEFT);
                $dueDate = Carbon::now()->addDays($daysUntilDue * $installment);
                $installmentAmount = $so->total_cost / $installments;
                $description = "Receivable for Sales Order {$so->order_number} - Installment $installment of $installments";

                Receivable::insert([
                    'code' => $code,
                    'description' => $description,
                    'client_id' => $so->client_id,
                    'amount' => $installmentAmount,
                    'due_date' => $dueDate,
                    'status' => $statusReceivable ? 'received' : 'pending',
                    'sales_order_id' => $so->id,
                    'reference_number' => $so->order_number,
                    'user_id' => $createdBy,
                ]);
            }

            $payableCode = 'PYB-'.$yearNow.'-'.str_pad((string) $payableCount, 6, '0', STR_PAD_LEFT);
            $payableDueDate = Carbon::now()->addDays($daysUntilDue * $installments);
            $payableDescription = "Payable for Products Sold in Sales Order {$so->order_number}";

            Payable::insert([
                'code' => $payableCode,
                'description' => $payableDescription,
                'vendor_id' => $so->vendor_id,
                'amount' => $so->product_value,
                'due_date' => $payableDueDate,
                'status' => $statusPayable ? 'paid' : 'pending',
                'payment_method' => $so->payment_method,
                'sales_order_id' => $so->id,
                'reference_number' => $so->order_number,
                'user_id' => $createdBy,
            ]);

            // Notify relevant users
            User::query()->whereHas('roles', fn ($q) => $q->whereIn('name', [RoleEnum::VENDOR->value, RoleEnum::FINANCE->value]))
                ->each(function (User $user) use ($so) {
                    $user->notify(new NewSalesOrder($so));
                });
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getEditDataForSelect(?string $search): array
    {
        $clientSearch = $search && str_starts_with($search, 'partner:') ? substr($search, 8) : null;
        $vendorSearch = $search && str_starts_with($search, 'vendor:') ? substr($search, 7) : null;

        $clients = Partner::query()
            ->select(['id', 'name'])
            ->whereType(PartnerTypeEnum::CLIENT->value)
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

        return [$clients, $vendors];
    }
}
