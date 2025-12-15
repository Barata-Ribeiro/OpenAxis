<?php

namespace App\Services\Product;

use App\Common\Helpers;
use App\Http\Requests\Product\ProductRequest;
use App\Interfaces\Product\ProductServiceInterface;
use App\Models\Product;
use App\Models\ProductCategory;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Str;

class ProductService implements ProductServiceInterface
{
    private bool $isSqlDriver;

    public function __construct()
    {
        $this->isSqlDriver = \in_array(DB::getDriverName(), ['mysql', 'pgsql']);
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

        [$start, $end] = Helpers::getDateRange($createdAtRange);

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

        $categoryId = ProductCategory::whereName($validatedData['category'])->value('id');

        $payload = collect($validatedData)
            ->except(['images', 'category'])
            ->put('product_category_id', $categoryId)
            ->toArray();

        DB::transaction(function () use ($payload, $images) {
            $product = Product::create($payload);

            foreach ($images as $image) {
                $product->addMedia($image)
                    ->usingFileName(Str::uuid7().'_'.now()->timestamp.'.'.$image->getClientOriginalExtension())
                    ->toMediaCollection('products_images');
            }
        });
    }

    public function updateProduct(ProductRequest $request, Product $product): void
    {
        $validatedData = $request->validated();

        $images = $validatedData['images'] ?? [];

        $categoryId = ProductCategory::whereName($validatedData['category'])->value('id');

        $payload = collect($validatedData)
            ->except(['images', 'category'])
            ->put('product_category_id', $categoryId)
            ->toArray();

        DB::transaction(function () use ($product, $payload, $images) {
            $product->update($payload);

            foreach ($images as $image) {
                $product->addMedia($image)
                    ->usingFileName(Str::uuid7().'_'.now()->timestamp.'.'.$image->getClientOriginalExtension())
                    ->toMediaCollection('products_images');
            }
        });
    }
}
