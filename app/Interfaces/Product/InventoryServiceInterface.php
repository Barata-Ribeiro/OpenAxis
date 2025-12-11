<?php

namespace App\Interfaces\product;

use App\Http\Requests\Product\AdjustInventoryRequest;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InventoryServiceInterface
{
    /**
     * Retrieve a paginated list of inventory items with optional sorting, searching, and filtering.
     *
     * This method returns a LengthAwarePaginator of inventory items, applying the supplied
     * pagination size, sort column/direction, global search string, and any additional
     * filtering criteria. Any null arguments should cause the implementation to fall
     * back to sensible defaults (e.g. default per-page size or sort order).
     *
     * @param  int|null  $perPage  Number of items per page; when null the service default is used.
     * @param  string|null  $sortBy  Column or attribute name to sort by.
     * @param  string|null  $sortDir  Sort direction ('asc'|'desc'); when null the service default is used.
     * @param  string|null  $search  Search term to be applied to relevant inventory fields (name, SKU, description, etc.).
     * @param  string|array|null  $filters  Additional filters to apply (e.g. associative array of field => value, filter objects, or a query callback).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Paginated collection of Inventory models.
     */
    public function getPaginatedInventory(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;

    /**
     * Adjust inventory for the given product according to the supplied request.
     *
     * @param  AdjustInventoryRequest  $request  Contains adjustment details.
     * @param  Product  $product  The product entity to adjust; implementations may mutate and persist this entity.
     */
    public function adjustInventory(AdjustInventoryRequest $request, Product $product): void;

    /**
     * Retrieve a paginated list of stock movements for a specific product with optional sorting, searching, and filtering.
     *
     * This method returns a LengthAwarePaginator of stock movements associated with the specified product,
     * applying the supplied pagination size, sort column/direction, global search string, and any additional
     * filtering criteria. Any null arguments should cause the implementation to fall
     * back to sensible defaults (e.g. default per-page size or sort order).
     *
     * @param  int|null  $productId  The ID of the product whose stock movements are to be retrieved.
     * @param  int|null  $perPage  Number of items per page; when null the service default is used.
     * @param  string|null  $sortBy  Column or attribute name to sort by.
     * @param  string|null  $sortDir  Sort direction ('asc'|'desc'); when null the service default is used.
     * @param  string|null  $search  Search term to be applied to relevant stock movement fields.
     * @param  string|array|null  $filters  Additional filters to apply (e.g. associative array of field => value, filter objects, or a query callback).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Paginated collection of StockMovement models.
     */
    public function getPaginatedStockMovements(?int $productId, ?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;
}
