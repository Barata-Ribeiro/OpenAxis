<?php

namespace App\Interfaces\Management;

use App\Http\Requests\Management\PurchaseOrderRequest;
use App\Http\Requests\Management\UpdatePurchaseOrderRequest;
use App\Models\PurchaseOrder;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PurchaseOrderServiceInterface
{
    /**
     * Retrieve a paginated list of purchase orders.
     *
     * Builds and returns a paginated collection of purchase orders using provided pagination,
     * sorting, search parameters, and additional filters.
     *
     * @param  int|null  $perPage  Number of items per page. If null, a sensible default is used.
     * @param  string|null  $sortBy  Column or attribute to sort the results by.
     * @param  string|null  $sortDir  Sort direction ('asc' or 'desc'). If null, a default direction is used.
     * @param  string|null  $search  Free-text search to filter purchase orders by its properties, or other searchable fields.
     * @param  mixed  $filters  Additional filters to apply. Accepted formats depend on implementation (e.g., associative array, closure, or filter object).
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of purchase orders.
     */
    public function getPaginatedPurchaseOrders(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;

    /**
     * Retrieve data required to populate select inputs for creating a purchase order.
     *
     * Returns an associative array containing lists of selectable items used by the
     * create form (for example suppliers, products, warehouses, etc.). When a
     * search term is provided, the returned lists should be filtered to match the
     * search.
     *
     * @param  string|null  $search  Optional search term to filter selectable items.
     * @return array Associative array of select data keyed by select name.
     */
    public function getCreateDataForSelect(?string $search): array;

    /**
     * Create a new purchase order from the provided request.
     *
     * @param  PurchaseOrderRequest  $request  The request containing purchase order data.
     *
     * @throws \InvalidArgumentException If the request data is invalid.
     * @throws \RuntimeException If the purchase order cannot be created or persisted.
     */
    public function createPurchaseOrder(PurchaseOrderRequest $request): void;

    /**
     * Retrieve a cursor-paginated list of suppliers suitable for populating a select control.
     *
     * If a search term is provided, the results will be filtered accordingly (e.g. by name or identifier).
     * The returned CursorPaginator should contain supplier records formatted for select usage (such as id/name pairs).
     *
     * @param  string|null  $search  Optional search term to filter suppliers.
     * @return CursorPaginator Cursor-paginated supplier records for use in select inputs.
     */
    public function getSuppliersForSelect(?string $search): CursorPaginator;

    /**
     * Update the given PurchaseOrder using data from the provided request.
     *
     * Applies validated input from UpdatePurchaseOrderRequest to the provided
     * PurchaseOrder model and persists the changes.
     *
     * @param  UpdatePurchaseOrderRequest  $request  Validated request containing update data.
     * @param  PurchaseOrder  $purchaseOrder  The purchase order model to update.
     */
    public function updatePurchaseOrder(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): void;
}
