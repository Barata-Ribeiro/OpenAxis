<?php

namespace App\Services\Product;

use App\Common\Helpers;
use App\Http\Requests\product\ProductRequest;
use App\Interfaces\Product\ProductServiceInterface;
use App\Models\Product;
use App\Models\ProductCategory;
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
        $categories = $filters['category_name'] ?? [];
        $is_active = $filters['is_active'][0] ?? null;

        $sortByStartsWithCategory = str_starts_with((string) $sortBy, 'category_name');

        if (! empty($sortBy) && $sortByStartsWithCategory) {
            $sortBy = str_replace('category_name', 'product_categories.name', $sortBy);
        }

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
            })
            ->when($createdAtRange, fn ($q) => $q->whereBetween('products.created_at', [$start, $end]))
            ->when($categories, fn ($q) => $q->whereHas('category', fn ($q2) => $q2->whereIn('name', $categories)))
            ->when($is_active, fn ($query) => $query->where('products.is_active', filter_var($is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
            ->when($sortByStartsWithCategory, fn ($qr) => $qr->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id'))
            ->with('category:id,name,slug', 'media')
            ->withTrashed()
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createProduct(ProductRequest $request): void
    {
        $validatedData = $request->validated();

        $images = $validatedData['images'] ?? [];
        $coverImage = null;
        $restOfImages = [];

        if (! empty($images)) {
            $coverImage = $images[0];

            if (count($images) > 1) {
                $restOfImages = array_values(array_slice($images, 1));
            }
        }

        $categoryId = ProductCategory::whereName($validatedData['category'])->value('id');

        // include product_category_id and remove non-fillable keys
        $validatedData['product_category_id'] = $categoryId;
        unset($validatedData['category']);

        $newProduct = Product::create($validatedData);

        $newProduct->addMedia($coverImage)
            ->withCustomProperties(['is_cover' => true])
            ->toMediaCollection('products_images');

        if (! empty($restOfImages)) {

            foreach ($restOfImages as $image) {
                $newProduct->addMedia($image)
                    ->withCustomProperties(['is_cover' => false])
                    ->toMediaCollection('products_images');
            }

        }
    }
}
