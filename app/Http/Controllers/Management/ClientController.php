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
        $clients = $this->getPaginatedClientsFromRequest($request);

        Log::info('Client: Accessed client list.', ['action_user_id' => Auth::id()]);

        return Inertia::render('erp/clients/index', [
            'clients' => $clients,
        ]);
    }

    public function show(Client $client)
    {
        Log::info('Client: Viewing client details.', ['client_id' => $client->id, 'action_user_id' => Auth::id()]);

        return Inertia::render('erp/clients/show', [
            'client' => $client->load('addresses'),
        ]);
    }

    public function create()
    {
        Log::info('Client: Accessing client creation form.', ['action_user_id' => Auth::id()]);

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
        Log::info('Client: Accessing client edit form.', ['client_id' => $client->id, 'action_user_id' => Auth::id()]);

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

    public function generateCsv(QueryRequest $request)
    {
        $userId = Auth::id();

        try {
            $clients = $this->getPaginatedClientsFromRequest($request);

            if ($clients->isEmpty()) {
                return back()->with('error', 'No clients found to generate CSV.');
            }

            return $this->clientService->generateCsvExport($clients);
        } catch (Exception $e) {
            Log::error('Client: Error generating clients CSV.', ['action_user_id' => $userId, 'error' => $e->getMessage()]);

            return back()->with('error', 'An unknown error occurred while generating the CSV export.');
        }
    }

    /**
     * Build and return a LengthAwarePaginator of clients based on the given request.
     *
     * Applies filtering, searching, sorting and eager-loading options provided by the
     * validated QueryRequest, then paginates the resulting query.
     *
     * Expected request inputs (handled/validated by QueryRequest):
     *  - page / per_page: pagination parameters
     *  - sort: sorting column/direction
     *  - filters: associative array of field => value
     *  - with: relations to eager-load
     *
     * @param  QueryRequest  $request  Validated query parameters for filtering, sorting and pagination.
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of Client models.
     */
    private function getPaginatedClientsFromRequest(QueryRequest $request)
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

        return $this->clientService->getPaginatedClients(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );
    }
}
