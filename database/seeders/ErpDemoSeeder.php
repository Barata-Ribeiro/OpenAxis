<?php

namespace Database\Seeders;

use App\Models\CashEntry;
use App\Models\Category;
use App\Models\Customer;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\StorageLocation;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ErpDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        DB::transaction(function (): void {
            $sku = 'DEMO-CLEANER';

            if (Product::withTrashed()->where('sku', $sku)->exists()) {
                return;
            }

            $location = StorageLocation::factory()->create(['code' => 'DEMO-A1', 'name' => 'Demo Warehouse A1']);
            $liter = UnitOfMeasure::where('code', 'L')->firstOrFail();
            $category = Category::factory()->create(['name' => 'Cleaning Supplies']);
            $product = Product::factory()->for($liter)->for($location)->create([
                'sku' => $sku,
                'name' => 'Neutral Floor Cleaner',
                'description' => 'Concentrated cleaner sold by the liter.',
                'cost_price_cents' => 800,
                'sale_price_cents' => 1_250,
            ]);
            $product->categories()->attach($category);

            $openingBatch = ProductBatch::factory()->for($product)->create();
            StockMovement::factory()->for($openingBatch)->for($location)->opening(60_000)->create([
                'quantity_milliunits' => 75_000,
                'occurred_at' => now()->subDays(7),
            ]);

            $supplier = Supplier::factory()->create(['name' => 'Demo Supply Company']);
            $purchase = Order::factory()->purchase($supplier)->confirmed()->create([
                'ordered_at' => now()->subDays(3),
                'external_reference' => 'DEMO-INVOICE-01',
            ]);
            $purchaseItem = OrderItem::factory()->for($purchase)->for($product)->create([
                'quantity_milliunits' => 25_000,
                'unit_price_cents' => 800,
            ]);

            $receivedBatch = ProductBatch::factory()->for($product)->create(['expires_on' => today()->addYear()]);
            StockMovement::factory()->for($receivedBatch)->for($location)->for($purchaseItem)->create([
                'quantity_milliunits' => $purchaseItem->quantity_milliunits,
                'cost_cents' => $purchaseItem->line_total_cents,
                'occurred_at' => $purchase->ordered_at,
            ]);

            $supplierCategory = ExpenseCategory::where('name', 'Suppliers')->firstOrFail();
            FinancialAccount::factory()->payable($supplierCategory)->for($purchase)->create([
                'description' => 'Demo supplier invoice',
                'amount_cents' => $purchase->totalCents(),
            ]);

            $customer = Customer::factory()->create(['name' => 'Demo Customer', 'phone' => '+55 11 5555-0100']);
            $sale = Order::factory()->confirmed()->for($customer)->create([
                'delivery_fee_type' => PaymentMethod::FEE_FIXED,
                'delivery_fee_rate' => 500,
            ]);

            $saleItem = OrderItem::factory()->for($sale)->for($product)->create([
                'quantity_milliunits' => 5_000,
                'unit_price_cents' => 1_250,
            ]);
            StockMovement::factory()->for($openingBatch)->for($location)->for($saleItem)->create([
                'type' => StockMovement::TYPE_ISSUE,
                'quantity_milliunits' => -$saleItem->quantity_milliunits,
            ]);

            $receivable = FinancialAccount::factory()->for($sale)->create([
                'description' => 'Demo sale on credit',
                'amount_cents' => $sale->totalCents(),
            ]);

            $pix = PaymentMethod::where('name', 'Pix')->firstOrFail();
            CashEntry::factory()->forAccount($receivable)->for($pix)->create([
                'description' => 'Partial payment for demo sale',
                'amount_cents' => 2_000,
            ]);

            $rent = ExpenseCategory::where('name', 'Rent')->firstOrFail();
            CashEntry::factory()->expense($rent)->for($pix)->create([
                'description' => 'Demo warehouse rent expense',
                'amount_cents' => 45_000,
            ]);
        });
    }
}
