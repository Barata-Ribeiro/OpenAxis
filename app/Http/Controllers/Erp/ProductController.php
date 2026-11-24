<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp;

use App\Http\Requests\Erp\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use InertiaUI\Modal\Modal;

final class ProductController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('erp/form', $this->form());
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $this->save(new Product, $request->validated());

        return $this->redirect($request);
    }

    public function show(Request $request, Product $product): Modal|Response
    {
        $product->load(['categories:id,name', 'unitOfMeasure', 'storageLocation']);

        return $this->modal($request, [
            'title' => $product->name,
            'kind' => 'product',
            'mode' => 'show',
            'details' => [
                'SKU' => $product->sku,
                'Description' => $product->description,
                'Unit of measure' => $product->unitOfMeasure->name,
                'Preferred location' => $product->storageLocation?->name,
                'Categories' => $product->categories->pluck('name')->implode(', '),
                'Current quantity' => $product->currentStockMilliunits() / 1000,
                'Reference unit cost (cents)' => $product->cost_price_cents,
                'Sale price (cents)' => $product->sale_price_cents,
            ],
        ]);
    }

    public function edit(Request $request, Product $product): Modal|Response
    {
        return $this->modal($request, $this->form($product));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $this->save($product, $request->validated());

        return $this->redirect($request);
    }

    public function archive(Request $request, Product $product): Modal|Response
    {
        return $this->modal($request, [
            'title' => 'Archive '.$product->name,
            'kind' => 'product',
            'mode' => 'archive',
            'id' => $product->getKey(),
        ]);
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($product): void {
            $locked = Product::query()->lockForUpdate()->findOrFail($product->id);

            if ($locked->currentStockMilliunits() !== 0) {
                throw ValidationException::withMessages(['record' => 'Only products with zero stock can be archived.']);
            }

            $locked->delete();
        }, attempts: 3);

        return $this->redirect($request);
    }

    /** @param array<string, mixed> $data */
    private function save(Product $product, array $data): void
    {
        $data['description'] ??= '';

        DB::transaction(function () use ($product, $data): void {
            $product->fill(Arr::except($data, ['category_ids', ...($product->exists ? ['sku', 'unit_of_measure_id'] : [])]))->save();
            $archived = $product->categories()->withTrashed()->whereNotNull('categories.deleted_at')->pluck('categories.id');
            $product->categories()->sync($archived->merge($data['category_ids'] ?? []));
        }, attempts: 3);
    }

    /** @return array<string, mixed> */
    private function form(?Product $product = null): array
    {
        return [
            'title' => $product === null ? 'New product' : 'Edit product',
            'kind' => 'product',
            'mode' => $product === null ? 'create' : 'edit',
            'id' => $product?->getKey(),
            'values' => $product === null ? [
                'cost_price_cents' => 0, 'sale_price_cents' => 0, 'minimum_stock_milliunits' => 0, 'is_active' => true,
            ] : [
                ...$product->only(['name', 'description', 'storage_location_id', 'cost_price_cents', 'sale_price_cents', 'minimum_stock_milliunits', 'is_active']),
                'category_ids' => $product->categories()->pluck('categories.id'),
            ],
            'choices' => [
                'unit_of_measure_id' => UnitOfMeasure::query()->orderBy('name')->get(['id', 'name']),
                'storage_location_id' => StorageLocation::query()->withTrashed()
                    ->where(fn ($query) => $query->where(fn ($active) => $active->whereNull('deleted_at')->where('is_active', true))
                        ->orWhere('id', $product?->storage_location_id))
                    ->orderBy('name')->get(['id', 'name']),
                'category_ids' => Category::query()->orderBy('name')->get(['id', 'name']),
            ],
        ];
    }

    /** @param array<string, mixed> $props */
    private function modal(Request $request, array $props): Modal|Response
    {
        return $request->user()->can('product.index')
            ? Inertia::modal('erp/form-modal', $props)->baseRoute('erp.inventory.index')
            : Inertia::render('erp/form', $props);
    }

    private function redirect(Request $request): RedirectResponse
    {
        if (! $request->user()->can('product.index')) {
            return to_route('dashboard');
        }

        $base = route('erp.inventory.index');
        $previous = $request->header(Modal::HEADER_BASE_URL) ?? url()->previous();

        return redirect()->to(strtok($previous, '?') === $base ? $previous : $base);
    }
}
