<?php

namespace App\Services\Product;

use App\Common\Helpers;
use App\Interfaces\Product\ProductServiceInterface;
use App\Models\Product;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService implements ProductServiceInterface
{
    private bool $isSqlDriver;

    public function __construct()
    {
        $this->isSqlDriver = in_array(DB::getDriverName(), ['mysql', 'pgsql']);
    }

    public function getPaginatedProducts(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $isSql = $this->isSqlDriver;

        $createdAtRange = $filters['created_at'] ?? [];
        $categories = $filters['categories'] ?? [];
        $is_active = $filters['is_active'][0] ?? null;

        $start = null;
        $end = null;

        if (! empty($createdAtRange) && count($createdAtRange) === 2) {
            $tz = config('app.timezone', 'UTC');
            $startTs = (int) $createdAtRange[0];
            $endTs = (int) $createdAtRange[1];

            $start = Carbon::createFromTimestampMs($startTs, $tz)
                ->startOfDay()
                ->clone()
                ->toDateTimeString();

            $end = Carbon::createFromTimestampMs($endTs, $tz)
                ->endOfDay()
                ->clone()
                ->toDateTimeString();
        }

        return Product::query()
            ->when($search, function ($qr) use ($search, $isSql) {
                if ($isSql) {
                    $booleanQuery = Helpers::buildBooleanQuery($search);
                    $qr->whereFullText(['sku', 'name', 'description'], $booleanQuery, ['mode' => 'boolean']);
                } else {
                    $qr->whenLike('sku', "%$search%")->orWhereLike('name', "%$search%")->orWhereLike('description', "%$search%");
                }
            })
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($categories, fn ($q) => $q->whereIn('category_id', $categories))
            ->when($is_active, fn ($query) => $query->where('is_active', filter_var($is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
            ->when(str_starts_with($sortBy, 'category.'), fn ($qr) => $qr->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id'))
            ->with('category:id,name,slug', 'media')
            ->withTrashed()
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }
}
