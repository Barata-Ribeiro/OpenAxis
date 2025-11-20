<?php

namespace App\Interfaces\Admin;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    /**
     * Retrieve a paginated list of users with optional sorting, searching and date-range filtering.
     *
     * @param  int|null  $perPage  Number of items per page. If null, the default pagination size is applied.
     * @param  string|null  $sortBy  Column or attribute name to sort by (e.g. 'name', 'email', 'created_at'). If null, a default sort column is used.
     * @param  string|null  $sortDir  Sort direction: 'asc' or 'desc'. If null, a default direction (usually 'asc') is used.
     * @param  string|null  $search  Search term to perform full- or partial-text matching against relevant user fields (name, email, etc.). If null or empty, no search filtering is applied.
     * @param  string|null  $startDate  Inclusive start date to filter users by a date field (e.g. created_at). Expected as a date string (e.g. 'YYYY-MM-DD' or ISO 8601). If null, no lower bound is applied.
     * @param  string|null  $endDate  Inclusive end date to filter users by a date field (e.g. created_at). Expected as a date string (e.g. 'YYYY-MM-DD' or ISO 8601). If null, no upper bound is applied.
     * @param  string|array|null  $filters  Additional filters to apply to the user query. If null or empty, no extra filtering is applied.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Paginated collection of users matching the provided criteria.
     */
    public function getPaginatedUsers(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, ?string $startDate, ?string $endDate, $filters): LengthAwarePaginator;

    /**
     * Create a new user and send a welcome email containing the provided password.
     *
     * Creates a new User with the given attributes and sends a NewUserMail to the
     * new user's email address with the plaintext password from the input data.
     *
     * @param  array  $data  Associative array of user attributes (must include 'email', 'name', and 'password').
     * @return \App\Models\User The created User instance.
     *
     * @throws \Throwable If user creation or email sending fails.
     */
    public function createUser(array $data): User;
}
