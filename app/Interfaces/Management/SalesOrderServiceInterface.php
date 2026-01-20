<?php

namespace App\Interfaces\Management;

use App\Http\Requests\Management\SaleOrderRequest;
use App\Http\Requests\Management\UpdateSaleOrderRequest;
use App\Models\SalesOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SalesOrderServiceInterface
{
    /**
     * Retrieve a paginated list of sales orders.
     *
     * Builds and returns a paginated collection of sales orders using provided pagination,
     * sorting, search parameters, and additional filters.
     *
     * @param  int|null  $perPage  Number of items per page. If null, a sensible default is used.
     * @param  string|null  $sortBy  Column or attribute to sort the results by.
     * @param  string|null  $sortDir  Sort direction ('asc' or 'desc'). If null, a default direction is used.
     * @param  string|null  $search  Free-text search to filter sales orders by its properties, or other searchable fields.
     * @param  mixed  $filters  Additional filters to apply. Accepted formats depend on implementation (e.g., associative array, closure, or filter object).
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of sales orders.
     */
    public function getPaginatedSalesOrders(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;

    /**
     * Retrieve data for populating a "create" select input, optionally filtered by a search term.
     *
     * Returns an array of options suitable for use in form select controls. Each option is typically
     * represented as an associative array with keys such as 'value' and 'label', or as an id => label map.
     *
     * @param  string|null  $search  Optional search string to filter the available options.
     * @return array<int|string, mixed> Array of select options (e.g. [['value' => ..., 'label' => ...], ...] or [id => label]).
     */
    public function getCreateDataForSelect(?string $search): array;

    /**
     * Create a new sales order from the provided request.
     *
     * @param  SaleOrderRequest  $request  The request containing sales order data.
     *
     * @throws \InvalidArgumentException If the request data is invalid.
     * @throws \RuntimeException If the sales order cannot be created or persisted.
     */
    public function createSalesOrder(SaleOrderRequest $request): void;

    /**
     * Retrieve option data for populating a select input on the sales-order edit form.
     *
     * If $search is provided, the returned options SHOULD be filtered by the term.
     * If $search is null, return the default/initial option set for the edit view.
     *
     * @param  string|null  $search  Optional search/filter term.
     * @return array<int, array{id: int|string, text: string}> Array of option arrays (may be empty if no matches).
     */
    public function getEditDataForSelect(?string $search): array;

    /**
     * Update the given sales order using data from the provided request.
     *
     * @param  UpdateSaleOrderRequest  $request  Validated request containing the update payload.
     * @param  SalesOrder  $salesOrder  The existing sales order instance to update and persist.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If the caller is not authorized to perform the update.
     * @throws \Illuminate\Validation\ValidationException If additional validation rules fail.
     * @throws \RuntimeException If persisting the update or related side effects fail.
     * @throws \Throwable For other unexpected errors.
     */
    public function updateSalesOrder(UpdateSaleOrderRequest $request, SalesOrder $salesOrder): void;
}
