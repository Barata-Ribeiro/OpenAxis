<?php

namespace App\Services\Management;

use App\Common\Helpers;
use App\Http\Requests\Management\ClientRequest;
use App\Interfaces\Management\ClientServiceInterface;
use App\Models\Client;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ClientService implements ClientServiceInterface
{
    public function getPaginatedClients(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $clientType = $filters['client_type'] ?? null;
        $createdAtRange = $filters['created_at'] ?? [];

        [$start, $end] = Helpers::getDateRange($createdAtRange);

        return Client::query()
            ->when($search, fn ($query, $search) => $query->whereLike('name', "%$search%")
                ->orWhereLike('email', "%$search%")->orWhereLike('phone_number', "%$search%")
                ->orWhereLike('identification', "%$search%"))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($clientType, fn ($q) => $q->whereIn('client_type', (array) $clientType))
            ->withTrashed()
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createClient(ClientRequest $request): void
    {
        $validated = $request->validated();

        $clientData = Arr::only($validated, [
            'name', 'email', 'identification', 'client_type',
        ]);

        $addressData = Arr::only($validated, [
            'type', 'label', 'street', 'number', 'complement', 'neighborhood',
            'city', 'state', 'postal_code', 'country', 'is_primary',
        ]);

        DB::transaction(fn () => Client::create($clientData)->addresses()->create($addressData));
    }
}
