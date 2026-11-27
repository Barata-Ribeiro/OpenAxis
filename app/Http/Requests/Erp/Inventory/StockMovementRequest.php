<?php

declare(strict_types=1);

namespace App\Http\Requests\Erp\Inventory;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\StorageLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Exists;
use SanderMuller\FluentValidation\Contracts\FluentRuleContract;
use SanderMuller\FluentValidation\FluentRule;
use SanderMuller\FluentValidation\HasFluentRules;

final class StockMovementRequest extends FormRequest
{
    use HasFluentRules;

    /** @return array<string, FluentRuleContract> */
    public function rules(): array
    {
        $receipt = $this->route('direction') === StockMovement::TYPE_RECEIPT;

        return [
            'product_id' => $receipt ? FluentRule::integer()->required()->exists(Product::class, 'id',
                fn (Exists $rule) => $rule->withoutTrashed()->where('is_active', true)) : FluentRule::field()->prohibited(),
            'product_batch_id' => $receipt ? FluentRule::field()->prohibited() : FluentRule::integer()->required()->exists(ProductBatch::class, 'id'),
            'storage_location_id' => FluentRule::integer()->required()->exists(StorageLocation::class, 'id',
                fn (Exists $rule) => $rule->withoutTrashed()->where('is_active', true)),
            'supplier_lot_code' => $receipt ? FluentRule::string()->nullable()->max(255) : FluentRule::field()->prohibited(),
            'expires_on' => $receipt ? FluentRule::date()->nullable()->format('Y-m-d') : FluentRule::field()->prohibited(),
            'quantity_milliunits' => FluentRule::integer()->required()->between(1, 1_000_000_000_000),
            'cost_cents' => $receipt ? FluentRule::integer()->nullable()->between(0, 1_000_000_000_000) : FluentRule::field()->prohibited(),
            'occurred_at' => FluentRule::date()->required()->format('Y-m-d\TH:i'),
            'notes' => FluentRule::string()->nullable()->max(5_000),
        ];
    }
}
