<?php

namespace App\Services\product;

use App\Common\Helpers;
use App\Http\Requests\Product\AdjustInventoryRequest;
use App\Interfaces\product\InventoryServiceInterface;
use App\Models\Product;
use App\Models\StockMovement;
use Auth;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class InventoryService implements InventoryServiceInterface
{
    private bool $isSqlDriver;

    public function __construct()
    {
        $this->isSqlDriver = \in_array(DB::getDriverName(), ['mysql', 'pgsql']);
    }

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
            ->when($search, function ($qr) use ($search, $isSql) {
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
            })->when($categories, fn ($q) => $q->whereHas('category', fn ($q2) => $q2->whereIn('name', $categories)))
            ->when($stock_status, function ($query) use ($stock_status) {
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
            ->when($is_active, fn ($query) => $query->where('products.is_active', filter_var($is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
            ->when($sortByStartsWithCategory, fn ($qr) => $qr->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id'))
            ->with('category:id,name,slug', 'media')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function adjustInventory(AdjustInventoryRequest $request, Product $product): void
    {
        $userId = Auth::id();

        $validated = $request->validated();
        $validated['user_id'] = $userId;

        DB::transaction(function () use ($validated, $product) {
            StockMovement::create($validated);

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
        });
    }
}
