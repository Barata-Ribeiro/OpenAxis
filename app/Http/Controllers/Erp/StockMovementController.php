<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp;

use App\Events\ErpDataChanged;
use App\Http\Requests\Erp\DataTableRequest;
use App\Http\Requests\Erp\StockMovementRequest;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\StorageLocation;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use InertiaUI\Modal\Modal;

final class StockMovementController extends Controller
{
    public function index(DataTableRequest $request): Response
    {
        return Inertia::render('erp/inventory/movements', [
            'table' => Inertia::live(function () use ($request): array {
                $rows = StockMovement::query()->toBase()
                    ->join('product_batches', 'stock_movements.product_batch_id', '=', 'product_batches.id')
                    ->join('products', 'product_batches.product_id', '=', 'products.id')
                    ->join('storage_locations', 'stock_movements.storage_location_id', '=', 'storage_locations.id')
                    ->select('stock_movements.id', 'stock_movements.type', 'stock_movements.occurred_at', 'product_batches.code as batch', 'products.name as product', 'storage_locations.name as location')
                    ->selectRaw('stock_movements.quantity_milliunits / 1000.0 AS quantity');
                $table = $request->tableQuery();
                $page = $table->paginate(DB::query()->fromSub($rows, 'movements'), [
                    'occurred_at' => 'date', 'type' => 'select', 'batch' => 'text',
                    'product' => 'text', 'location' => 'text', 'quantity' => 'number',
                ], ['product', 'batch', 'location'], 'occurred_at', true);

                return $table->response($page);
            }, on: new ErpDataChanged('movements')),
        ]);
    }

    public function create(Request $request): Response
    {
        $receipt = $request->route('direction') === StockMovement::TYPE_RECEIPT;
        $choices = [
            'storage_location_id' => StorageLocation::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ];

        if ($receipt) {
            $choices['product_id'] = Product::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku'])
                ->map(fn (Product $product): array => ['id' => $product->id, 'name' => "$product->sku · $product->name"]);
        } else {
            $choices['product_batch_id'] = ProductBatch::query()
                ->whereHas('stockMovements', fn ($query) => $query->selectRaw('SUM(quantity_milliunits)')->groupBy('product_batch_id')->havingRaw('SUM(quantity_milliunits) > 0'))
                ->with('product:id,name')->orderBy('code')->get()
                ->map(fn (ProductBatch $batch): array => ['id' => $batch->id, 'name' => $batch->code.' · '.$batch->product->name]);
        }

        return Inertia::render('erp/form', [
            'title' => $receipt ? 'New stock receipt' : 'New stock issue',
            'kind' => $receipt ? 'stock-receipt' : 'stock-issue',
            'mode' => 'create',
            'values' => ['occurred_at' => now()->format('Y-m-d\TH:i')],
            'choices' => $choices,
        ]);
    }

    public function store(StockMovementRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $receipt = $request->route('direction') === StockMovement::TYPE_RECEIPT;

        try {
            DB::transaction(function () use ($data, $receipt, $request): void {
                $location = StorageLocation::query()->where('is_active', true)->sharedLock()->find((int) $data['storage_location_id']);

                if ($location === null) {
                    throw ValidationException::withMessages(['storage_location_id' => 'This location is unavailable.']);
                }

                $batch = $receipt
                    ? Product::query()->where('is_active', true)->lockForUpdate()->findOrFail((int) $data['product_id'])->batches()->create([
                        'supplier_lot_code' => $data['supplier_lot_code'] ?? null,
                        'expires_on' => $data['expires_on'] ?? null,
                    ])
                    : ProductBatch::query()->findOrFail((int) $data['product_batch_id']);

                $batch->stockMovements()->create([
                    'type' => $receipt ? StockMovement::TYPE_RECEIPT : StockMovement::TYPE_ISSUE,
                    'quantity_milliunits' => ($receipt ? 1 : -1) * (int) $data['quantity_milliunits'],
                    'cost_cents' => $receipt ? ($data['cost_cents'] ?? null) : null,
                    'storage_location_id' => $location->id,
                    'created_by_id' => $request->user()->id,
                    'occurred_at' => Carbon::createFromFormat('Y-m-d\TH:i', $data['occurred_at'])->startOfMinute(),
                    'notes' => $data['notes'] ?? null,
                ]);
            }, attempts: 3);
        } catch (DomainException $exception) {
            throw ValidationException::withMessages(['quantity_milliunits' => $exception->getMessage()]);
        }

        return to_route(match (true) {
            $request->user()->can('stock-movement.index') => 'erp.inventory.movements.index',
            $request->user()->can('product.index') => 'erp.inventory.index',
            default => 'dashboard',
        });
    }

    public function show(Request $request, StockMovement $stockMovement): Modal|Response
    {
        $stockMovement->load(['productBatch.product', 'storageLocation', 'createdBy']);
        $props = [
            'title' => 'Stock movement',
            'kind' => 'stock-movement',
            'mode' => 'show',
            'details' => [
                'Product' => $stockMovement->productBatch->product->name,
                'Batch' => $stockMovement->productBatch->code,
                'Location' => $stockMovement->storageLocation->name,
                'Quantity' => $stockMovement->quantity_milliunits / 1000,
                'Type' => ucfirst($stockMovement->type),
                'Recorded by' => $stockMovement->createdBy?->name,
                'Date and time' => $stockMovement->occurred_at->toDateTimeString(),
                'Notes' => $stockMovement->notes,
            ],
        ];

        return $request->user()->can('stock-movement.index')
            ? Inertia::modal('erp/form-modal', $props)->baseRoute('erp.inventory.movements.index')
            : Inertia::render('erp/form', $props);
    }
}
