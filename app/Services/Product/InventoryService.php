<?php

namespace App\Services\Product;

use App\Common\Helpers;
use App\Enums\RoleEnum;
use App\Http\Requests\Product\AdjustInventoryRequest;
use App\Interfaces\Product\InventoryServiceInterface;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Notifications\ManualSupplyAdjustment;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use Log;
use Number;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InventoryService implements InventoryServiceInterface
{
    private bool $isSqlDriver;

    public function __construct()
    {
        $this->isSqlDriver = \in_array(DB::getDriverName(), ['mysql', 'pgsql']);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaginatedInventory(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $isSql = $this->isSqlDriver;

        $categories = $filters['category_name'] ?? [];
        $stock_status = $filters['current_stock'][0] ?? null;
        $is_active = $filters['is_active'][0] ?? null;

        $sortByStartsWithCategory = str_starts_with((string) $sortBy, 'category_name');

        if (! empty($sortBy) && $sortByStartsWithCategory) {
            $sortBy = str_replace('category_name', 'product_categories.name', $sortBy);
        }

        return Product::query()
            ->select('products.*')
            ->when($search, function (Builder $qr) use ($search, $isSql) {
                if ($isSql) {
                    $booleanQuery = Helpers::buildBooleanQuery($search);
                    $qr->whereFullText(['products.sku', 'products.name', 'products.description'], $booleanQuery, ['mode' => 'boolean']);
                } else {
                    $qr->where(function ($q) use ($search) {
                        $q->whereLike('products.sku', "%$search%")
                            ->orWhereLike('products.name', "%$search%")
                            ->orWhereLike('products.description', "%$search%");
                    });
                }
            })->when($categories, fn (Builder $query) => $query->whereHas('category', fn ($q2) => $q2->whereIn('name', $categories)))
            ->when($stock_status, function (Builder $query) use ($stock_status) {
                switch ($stock_status) {
                    case 'in_stock':
                        $query->where('products.current_stock', '>', 0);
                        break;
                    case 'below_minimum':
                        $query->whereColumn('products.current_stock', '<', 'products.minimum_stock');
                        break;
                    case 'out_of_stock':
                        $query->where('products.current_stock', '=', 0);
                        break;
                    default:
                        break;
                }
            })
            ->when($is_active, fn (Builder $query) => $query->where('products.is_active', filter_var($is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
            ->when($sortByStartsWithCategory, fn (Builder $qr) => $qr->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id'))
            ->with('category:id,name,slug', 'media')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * {@inheritDoc}
     */
    public function adjustInventory(AdjustInventoryRequest $request, Product $product): void
    {
        $userId = Auth::id();

        $validated = $request->validated();
        $validated['user_id'] = $userId;

        DB::transaction(function () use ($validated, $product) {
            $stockMovement = StockMovement::create($validated);

            switch ($validated['movement_type']) {
                case 'inbound':
                    $product->increment('current_stock', $validated['quantity']);
                    break;
                case 'outbound':
                    $product->decrement('current_stock', $validated['quantity']);
                    break;
                case 'adjustment':
                    $product->current_stock = $validated['quantity'];
                    break;
                default:
                    throw new InvalidArgumentException('Invalid movement_type: '.($validated['movement_type'] ?? 'null'));
            }

            $product->save();

            User::query()->whereHas('roles', fn ($q) => $q->where('name', RoleEnum::VENDOR->value))
                ->each(function (User $user) use ($stockMovement) {
                    $user->notify(new ManualSupplyAdjustment($stockMovement));
                });
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getPaginatedStockMovements(?int $productId, ?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $userId = Auth::id();

        $createdAtRange = $filters['created_at'] ?? [];
        $moveType = $filters['movement_type'] ?? [];

        [$start, $end] = Helpers::getDateRange($createdAtRange);

        return StockMovement::query()
            ->whereProductId($productId)
            ->whereUserId($userId)
            ->when($search, fn ($qr) => $qr->whereLike('movement_type', "%$search%")
                ->orWhereLike('reason', "%$search%")->orWhereLike('reference', "%$search%"))
            ->when($moveType, fn ($qr) => $qr->whereIn('movement_type', $moveType))
            ->when($createdAtRange, fn ($qr) => $qr->whereBetween('created_at', [$start, $end]))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * {@inheritDoc}
     */
    public function getProductsForSelect(?string $search): CursorPaginator
    {
        $isSql = $this->isSqlDriver;

        $paginator = Product::query()
            ->select(['id', 'name', 'sku', 'description'])
            ->orderByDesc('id')
            ->whereIsActive(true)
            ->when($search, function ($qr) use ($search, $isSql) {
                if ($isSql) {
                    $booleanQuery = Helpers::buildBooleanQuery($search);
                    $qr->whereFullText(['sku', 'name', 'description'], $booleanQuery, ['mode' => 'boolean']);
                } else {
                    $qr->where(function ($q) use ($search) {
                        $q->whereLike('sku', "%$search%")
                            ->orWhereLike('name', "%$search%")
                            ->orWhereLike('description', "%$search%");
                    });
                }
            })
            ->cursorPaginate(10)
            ->withQueryString();

        foreach ($paginator->items() as $item) {
            $item->makeHidden(['sku', 'description'])->setAppends([]);
        }

        return $paginator;
    }

    /**
     * {@inheritDoc}
     */
    public function generateCsvExport(LengthAwarePaginator $inventory): BinaryFileResponse
    {
        $finalFilename = Carbon::now()->format('Y_m_d_H_i_s').'_inventory_export.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$finalFilename\"",
        ];

        $csvFileName = tempnam(sys_get_temp_dir(), 'csv_'.Str::ulid()).'.csv';
        $openFile = fopen($csvFileName, 'w');

        fwrite($openFile, "\xEF\xBB\xBF");

        $delimiter = ';';
        $header = [
            'ID',
            'SKU',
            'Name',
            'Category',
            'Current Stock',
            'Minimum Stock',
            'Comission',
            'Selling Price',
            'Is Active',
        ];

        fputcsv($openFile, $header, $delimiter);

        foreach ($inventory->items() as $product) {
            $row = [
                $product->id,
                $product->sku,
                $product->name,
                $product->category->name ?? 'Uncategorized',
                $product->current_stock,
                $product->minimum_stock,
                Number::percentage($product->comission),
                Number::currency($product->selling_price),
                $product->is_active ? 'Yes' : 'No',
            ];

            fputcsv($openFile, $row, $delimiter);
        }

        fclose($openFile);

        Log::info('Inventory: Generated inventory CSV export.', [
            'action_user_id' => Auth::id(),
            'record_count' => $inventory->total(),
        ]);

        return response()->download($csvFileName, $finalFilename, $headers)->deleteFileAfterSend(true);

    }
}
