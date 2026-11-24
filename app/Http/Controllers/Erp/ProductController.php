<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp;

use App\Http\Requests\Erp\ProductRequest;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Image\ImageException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Image as LaravelImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use InertiaUI\Modal\Modal;

final class ProductController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('erp/inventory/products/create', $this->formData());
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
            'images' => $this->images($product),
            'details' => [
                'SKU' => $product->sku,
                'Description' => $product->description,
                'Unit of measure' => $product->unitOfMeasure->name,
                'Preferred location' => $product->storageLocation?->name,
                'Categories' => $product->categories->pluck('name')->implode(', '),
                'Current quantity' => $product->currentStockMilliunits() / 1000,
                'Reference unit cost' => ['cents' => $product->cost_price_cents],
                'Sale price' => ['cents' => $product->sale_price_cents],
            ],
        ]);
    }

    public function edit(Request $request, Product $product): Modal|Response
    {
        return $this->modal($request, [
            'title' => 'Edit '.$product->name,
            'kind' => 'product',
            'mode' => 'edit',
            'id' => $product->id,
            'productForm' => $this->formData($product),
        ]);
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
            if ($product->exists) {
                $product = Product::query()->lockForUpdate()->findOrFail($product->id);
            }

            $attributes = Arr::except($data, ['category_ids', 'images', 'remove_image_ids', ...($product->exists ? ['sku', 'unit_of_measure_id'] : [])]);
            $product->fill($attributes)->save();
            $archived = $product->categories()->withTrashed()->whereNotNull('categories.deleted_at')->pluck('categories.id');
            $product->categories()->sync($archived->merge($data['category_ids'] ?? []));
            $this->saveImages($product, $data['images'] ?? [], $data['remove_image_ids'] ?? []);
        });
    }

    /**
     * @param  list<UploadedFile>  $files
     * @param  list<int|numeric-string>  $removeIds
     */
    private function saveImages(Product $product, array $files, array $removeIds): void
    {
        if ($files === [] && $removeIds === []) {
            return;
        }

        $existing = $product->images()->get();
        $removed = $existing->whereIn('id', $removeIds);

        if ($removed->count() !== count($removeIds)) {
            throw ValidationException::withMessages(['remove_image_ids' => 'Select images belonging to this product.']);
        }

        if ($files !== [] && $existing->count() - $removed->count() + count($files) > 5) {
            throw ValidationException::withMessages(['images' => 'A product can have at most five images.']);
        }

        $directory = 'products/'.$product->id;
        $disk = Storage::disk('public');
        $sortOrder = (int) $existing->max('sort_order');

        foreach ($removed as $image) {
            $image->delete();

            if ($image->disk === 'public' && str_starts_with($image->path, $directory.'/')) {
                DB::afterCommit(fn () => $disk->delete($image->path));
            }
        }

        foreach ($files as $index => $file) {
            try {
                $path = LaravelImage::fromUpload($file)->orient()->scale(1600, 1600)->toWebp()->storePublicly($directory, 'public');
            } catch (ImageException $exception) {
                report($exception);
                $path = false;
            }

            if ($path === false) {
                throw ValidationException::withMessages(["images.$index" => 'This image could not be processed.']);
            }

            DB::afterRollBack(fn () => $disk->delete($path));
            $product->images()->create(['disk' => 'public', 'path' => $path, 'sort_order' => ++$sortOrder]);
        }
    }

    /** @return array<string, mixed> */
    private function formData(?Product $product = null): array
    {
        return [
            'product' => $product === null ? null : [
                ...$product->only(['name', 'description', 'storage_location_id', 'cost_price_cents', 'sale_price_cents', 'minimum_stock_milliunits', 'is_active']),
                'category_ids' => $product->categories()->pluck('categories.id'),
            ],
            'units' => $product === null ? UnitOfMeasure::query()->orderBy('name')->get(['id', 'name']) : [],
            'locations' => StorageLocation::query()->withTrashed()
                ->where(fn ($query) => $query->where(fn ($active) => $active->whereNull('deleted_at')->where('is_active', true))
                    ->orWhere('id', $product?->storage_location_id))
                ->orderBy('name')->get(['id', 'name']),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'images' => $product === null ? [] : $this->images($product),
        ];
    }

    /** @return list<array{id: int, url: string|null}> */
    private function images(Product $product): array
    {
        return array_values($product->images()->get()->map(fn (Image $image): array => [
            'id' => $image->id,
            'url' => $image->disk === 'public' ? Storage::disk('public')->url($image->path) : null,
        ])->all());
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
