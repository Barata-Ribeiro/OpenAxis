<?php

declare(strict_types=1);

namespace App\Http\Requests\Erp\Inventory;

use App\Models\Category;
use App\Models\Product;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Exists;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\HasFluentRules;

final class ProductRequest extends FormRequest
{
    use HasFluentRules;

    /** @return array<string, FluentRuleContract> */
    public function rules(): array
    {
        $product = $this->route('product');
        $editing = $product instanceof Product;
        $currentLocation = $editing ? $product->storage_location_id : null;

        return [
            'sku' => $editing ? FluentRule::field()->prohibited() : FluentRule::string()->required()->max(64)->unique(Product::class),
            'unit_of_measure_id' => $editing ? FluentRule::field()->prohibited() : FluentRule::integer()->required()
                ->exists(UnitOfMeasure::class, 'id', fn (Exists $rule) => $rule->withoutTrashed()),
            'name' => FluentRule::string()->required()->max(180),
            'description' => FluentRule::string()->nullable()->max(10_000),
            'storage_location_id' => FluentRule::integer()->nullable()
                ->exists(StorageLocation::class, 'id', fn (Exists $rule) => $rule->where(
                    fn (Builder $query) => $query->where(fn (Builder $active) => $active->whereNull('deleted_at')->where('is_active', true))
                        ->orWhere('id', $currentLocation)
                )),
            'cost_price_cents' => FluentRule::integer()->required()->between(0, 4_294_967_295),
            'sale_price_cents' => FluentRule::integer()->required()->between(0, 4_294_967_295),
            'minimum_stock_milliunits' => FluentRule::integer()->required()->between(0, 1_000_000_000_000),
            'is_active' => FluentRule::boolean()->required(),
            'category_ids' => FluentRule::list()->max(50)->each(
                FluentRule::integer()->required()->distinct()
                    ->exists(Category::class, 'id', fn (Exists $rule) => $rule->withoutTrashed()),
            ),
            'images' => FluentRule::list()->max(5)->each(
                FluentRule::image()->required()->mimes('jpg', 'jpeg', 'png', 'webp')
                    ->max('2mb')->maxWidth(4096)->maxHeight(4096),
            ),
            'remove_image_ids' => $editing
                ? FluentRule::list()->max(5)->each(FluentRule::integer()->required()->min(1)->distinct())
                : FluentRule::field()->prohibited(),
        ];
    }
}
