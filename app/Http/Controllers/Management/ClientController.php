<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\ClientRequest;
use App\Http\Requests\Management\UpdateClientRequest;
use App\Http\Requests\QueryRequest;
use App\Models\Client;
use App\Services\Management\ClientService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

class ClientController extends Controller
{
    public function __construct(private ClientService $clientService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'name', 'email', 'identification', 'client_type', 'created_at', 'updated_at'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $clients = $this->clientService->getPaginatedClients(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        return Inertia::render('erp/clients/index', [
            'clients' => $clients,
        ]);
    }

    public function create()
    {
        return Inertia::render('erp/clients/create');
    }

    public function store(ClientRequest $request)
    {
        $userId = Auth::id();

        try {
            Log::info('Client: Creation of new a new client.', ['action_user_id' => $userId]);

            $this->clientService->createClient($request);

            return to_route('erp.clients.index')->with('success', 'Client created successfully.');
        } catch (Exception $e) {
            Log::error('Client: Error creating new client.', [
                'action_user_id' => $userId,
                'error_message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Error creating client.');
        }
    }

    public function edit(Client $client)
    {
        return Inertia::render('erp/clients/edit', [
            'client' => $client,
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $userId = Auth::id();
        $data = $request->validated();

        try {
            Log::info('Client: Updating client.', ['action_user_id' => $userId]);

            $client->update($data);

            return to_route('erp.clients.index')->with('success', 'Client updated successfully.');
        } catch (Exception $e) {
            Log::error('Client: Error updating client.', [
                'action_user_id' => $userId,
                'error_message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Error updating client.');
        }
    }

    public function destroy(Client $client)
    {
        $userId = Auth::id();

        try {
            Log::info('Client: Deleting client.', ['action_user_id' => $userId]);

            $client->delete();

            return to_route('erp.clients.index')->with('success', 'Client deleted successfully.');
        } catch (Exception $e) {
            Log::error('Client: Error deleting client.', [
                'action_user_id' => $userId,
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Error deleting client.');
        }
    }

    public function forceDestroy(Client $client)
    {
        $userId = Auth::id();

        try {
            Log::info('Client: Permanently deleting client.', ['action_user_id' => $userId]);

            $client->forceDelete();

            return to_route('erp.clients.index')->with('success', 'Client permanently deleted successfully.');
        } catch (Exception $e) {
            Log::error('Client: Error permanently deleting client.', [
                'action_user_id' => $userId,
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'An unknown error occurred while permanently deleting the client.');
        }
    }
}
