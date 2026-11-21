<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $type
 * @property int|null $financial_account_id
 * @property int|null $expense_category_id
 * @property int $payment_method_id
 * @property int|null $created_by_id
 * @property string $description
 * @property int $amount_cents
 * @property string $fee_type
 * @property int $fee_rate
 * @property int $fee_cents
 * @property \Carbon\CarbonImmutable $occurred_at
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\ExpenseCategory|null $expenseCategory
 * @property-read \App\Models\FinancialAccount|null $financialAccount
 * @property-read \App\Models\PaymentMethod $paymentMethod
 * @method static \Database\Factories\CashEntryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereAmountCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereExpenseCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereFeeCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereFeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereFinancialAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereOccurredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashEntry whereType($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCashEntry {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int|null $parent_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCategory {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property string|null $email
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @method static \Database\Factories\CustomerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCustomer {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property bool $is_system
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CashEntry> $cashEntries
 * @property-read int|null $cash_entries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FinancialAccount> $financialAccounts
 * @property-read int|null $financial_accounts_count
 * @method static \Database\Factories\ExpenseCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereIsSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperExpenseCategory {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $type
 * @property int|null $order_id
 * @property int|null $expense_category_id
 * @property int|null $created_by_id
 * @property string $description
 * @property string|null $contact_name
 * @property string|null $contact_phone
 * @property int $amount_cents
 * @property \Carbon\CarbonImmutable $due_on
 * @property \Carbon\CarbonImmutable|null $cancelled_at
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CashEntry> $cashEntries
 * @property-read int|null $cash_entries_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\ExpenseCategory|null $expenseCategory
 * @property-read \App\Models\Order|null $order
 * @property-read string $status
 * @method static \Database\Factories\FinancialAccountFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereAmountCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereDueOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereExpenseCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFinancialAccount {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $imageable_type
 * @property int $imageable_id
 * @property string $disk
 * @property string $path
 * @property int $sort_order
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model $imageable
 * @method static \Database\Factories\ImageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereImageableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereImageableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperImage {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $number
 * @property string $type
 * @property string $status
 * @property int|null $customer_id
 * @property int|null $supplier_id
 * @property int|null $created_by_id
 * @property \Carbon\CarbonImmutable $ordered_at
 * @property string|null $external_reference
 * @property string|null $delivery_fee_type
 * @property int $delivery_fee_rate
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FinancialAccount> $financialAccounts
 * @property-read int|null $financial_accounts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Supplier|null $supplier
 * @method static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryFeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereExternalReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOrder {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property string $product_sku
 * @property string $product_name
 * @property string $unit_code
 * @property int $quantity_milliunits
 * @property int $unit_price_cents
 * @property int $line_total_cents
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @method static \Database\Factories\OrderItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereLineTotalCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantityMilliunits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUnitCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUnitPriceCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOrderItem {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $fee_type
 * @property int $fee_rate
 * @property bool $is_active
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CashEntry> $cashEntries
 * @property-read int|null $cash_entries_count
 * @method static \Database\Factories\PaymentMethodFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereFeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPaymentMethod {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $unit_of_measure_id
 * @property int|null $storage_location_id
 * @property string $sku
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int $cost_price_cents
 * @property int $sale_price_cents
 * @property int $minimum_stock_milliunits
 * @property bool $is_active
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductBatch> $batches
 * @property-read int|null $batches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read \App\Models\StorageLocation|null $storageLocation
 * @property-read \App\Models\UnitOfMeasure $unitOfMeasure
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCostPriceCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMinimumStockMilliunits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSalePriceCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStorageLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUnitOfMeasureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProduct {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property int $sequence
 * @property string $code
 * @property string|null $supplier_lot_code
 * @property \Carbon\CarbonImmutable|null $expires_on
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @method static \Database\Factories\ProductBatchFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductBatch whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductBatch whereExpiresOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductBatch whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductBatch whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductBatch whereSupplierLotCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductBatch whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProductBatch {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_batch_id
 * @property int $storage_location_id
 * @property int|null $order_item_id
 * @property int|null $return_of_id
 * @property int|null $created_by_id
 * @property string $type
 * @property int $quantity_milliunits
 * @property int|null $cost_cents
 * @property \Carbon\CarbonImmutable $occurred_at
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\OrderItem|null $orderItem
 * @property-read StockMovement|null $originalMovement
 * @property-read \App\Models\ProductBatch $productBatch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StockMovement> $returns
 * @property-read int|null $returns_count
 * @property-read \App\Models\StorageLocation $storageLocation
 * @method static \Database\Factories\StockMovementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereCostCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereOccurredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereOrderItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereProductBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereQuantityMilliunits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereReturnOfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereStorageLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereType($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStockMovement {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property bool $is_active
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @method static \Database\Factories\StorageLocationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageLocation withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStorageLocation {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property string|null $email
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @method static \Database\Factories\SupplierFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSupplier {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $quantity_decimals
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Database\Factories\UnitOfMeasureFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure whereQuantityDecimals($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitOfMeasure withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUnitOfMeasure {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Carbon\CarbonImmutable|null $two_factor_confirmed_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passkeys\Passkey> $passkeys
 * @property-read int|null $passkeys_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $teams
 * @property-read int|null $teams_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User isSuperAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, ?string $guard = null, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User team($teams, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, ?string $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTeam($teams)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

