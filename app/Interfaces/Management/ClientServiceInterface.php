<?php

namespace App\Interfaces\Management;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ClientServiceInterface
{
    public function getPaginatedClients(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator;
}
