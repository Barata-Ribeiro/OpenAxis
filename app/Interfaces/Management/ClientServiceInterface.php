<?php

namespace App\Interfaces\Management;

use App\Http\Requests\Management\ClientRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ClientServiceInterface
{
    /**
     * Retrieve a paginated list of clients.
     *
     * Builds and returns a paginated collection of clients using provided pagination,
     * sorting, search parameters, and additional filters.
     *
     * @param  int|null  $perPage  Number of items per page. If null, a sensible default is used.
     * @param  string|null  $sortBy  Column or attribute to sort the results by.
     * @param  string|null  $sortDir  Sort direction ('asc' or 'desc'). If null, a default direction is used.
     * @param  string|null  $search  Free-text search to filter clients by name, email, or other searchable fields.
     * @param  mixed  $filters  Additional filters to apply. Accepted formats depend on implementation (e.g., associative array, closure, or filter object).
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of clients.
     */
    public function getPaginatedClients(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;

    /**
     * Create a new client from the provided request data.
     *
     * This method validates the incoming ClientRequest and persists a new client
     * record. Implementations may also trigger related side effects (for example,
     * dispatching domain events or sending notifications).
     *
     * @param  ClientRequest  $request  The request object containing the client's data.
     *
     * @throws \InvalidArgumentException If the provided request is invalid.
     * @throws \RuntimeException If the client could not be created due to persistence or runtime errors.
     */
    public function createClient(ClientRequest $request): void;
}
