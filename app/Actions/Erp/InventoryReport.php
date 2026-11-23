<?php

declare(strict_types=1);

namespace App\Actions\Erp;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use stdClass;

final class InventoryReport
{
    private const int EXPIRING_DAYS = 30;

    /** @return array<string, mixed> */
    public function table(TableQuery $table): array
    {
        $fields = [
            'sku' => 'text', 'name' => 'text', 'quantity' => 'number', 'unit' => 'text',
            'expires_on' => 'date', 'days_remaining' => 'number', 'status' => 'select',
            'minimum_stock' => 'number', 'category' => 'text', 'average_cost' => 'number', 'current_value' => 'number',
        ];
        $page = $table->paginate($this->rows(), $fields, ['sku', 'name', 'description', 'category'], 'name');
        $products = Product::query()->with([
            'images' => fn ($images) => $images->limit(1),
            'categories:id,name',
        ])->whereKey($page->getCollection()->pluck('id'))->get()->keyBy('id');

        $rows = $page->getCollection()->map(function (stdClass $row) use ($products): array {
            $product = $products->get($row->id);
            $image = $product?->images->first();

            return [
                'id' => $row->id,
                'sku' => $row->sku,
                'name' => $row->name,
                'quantity' => (float) $row->quantity,
                'unit' => $row->unit,
                'expires_on' => $row->expires_on,
                'days_remaining' => $row->days_remaining === null ? null : (int) $row->days_remaining,
                'status' => $row->status,
                'minimum_stock' => (float) $row->minimum_stock,
                'category' => $product?->categories->sortBy('name')->pluck('name')->implode(', '),
                'average_cost' => (float) $row->average_cost,
                'current_value' => (float) $row->current_value,
                'image' => $image?->disk === 'public' ? Storage::disk('public')->url($image->path) : null,
            ];
        });

        return $table->response($page, array_values($rows->all()));
    }

    /** @return array{products: int, critical: int, in_stock: float, expiring: int, expired: int} */
    public function summary(): array
    {
        $summary = $this->rows()->selectRaw('
            COUNT(*) AS products,
            COALESCE(SUM(CASE WHEN quantity <= minimum_stock THEN 1 ELSE 0 END), 0) AS critical,
            COALESCE(SUM(quantity), 0) AS in_stock,
            COALESCE(SUM(CASE WHEN expiring = 1 THEN 1 ELSE 0 END), 0) AS expiring,
            COALESCE(SUM(CASE WHEN expired = 1 THEN 1 ELSE 0 END), 0) AS expired
        ')->first();

        return [
            'products' => (int) $summary->products,
            'critical' => (int) $summary->critical,
            'in_stock' => (float) $summary->in_stock,
            'expiring' => (int) $summary->expiring,
            'expired' => (int) $summary->expired,
        ];
    }

    private function rows(): Builder
    {
        $ledger = StockMovement::query()->toBase()
            ->select('product_batch_id')
            ->selectRaw('SUM(quantity_milliunits) AS balance')
            ->selectRaw('SUM(CASE WHEN type IN (?, ?) AND cost_cents IS NOT NULL THEN quantity_milliunits ELSE 0 END) AS priced_units', [
                StockMovement::TYPE_OPENING, StockMovement::TYPE_RECEIPT,
            ])
            ->selectRaw('SUM(CASE WHEN type IN (?, ?) THEN COALESCE(cost_cents, 0) ELSE 0 END) AS acquisition_cost', [
                StockMovement::TYPE_OPENING, StockMovement::TYPE_RECEIPT,
            ])
            ->groupBy('product_batch_id');

        $today = today()->toDateString();
        $limit = today()->addDays(self::EXPIRING_DAYS)->toDateString();
        $batches = ProductBatch::query()->toBase()->joinSub($ledger, 'ledger', 'ledger.product_batch_id', 'product_batches.id')
            ->select('product_id')
            ->selectRaw('SUM(ledger.balance) AS balance')
            ->selectRaw('SUM(COALESCE(ledger.balance * (ledger.acquisition_cost * 1.0 / NULLIF(ledger.priced_units, 0)), 0)) AS valued_stock')
            ->selectRaw('SUM(CASE WHEN ledger.priced_units = 0 THEN ledger.balance ELSE 0 END) AS unpriced_stock')
            ->selectRaw('MIN(CASE WHEN ledger.balance > 0 THEN DATE(expires_on) END) AS expires_on')
            ->selectRaw('MAX(CASE WHEN ledger.balance > 0 AND DATE(expires_on) < ? THEN 1 ELSE 0 END) AS expired', [$today])
            ->selectRaw('MAX(CASE WHEN ledger.balance > 0 AND DATE(expires_on) BETWEEN ? AND ? THEN 1 ELSE 0 END) AS expiring', [$today, $limit])
            ->groupBy('product_id');

        $category = DB::table('category_product')->join('categories', 'categories.id', '=', 'category_product.category_id')
            ->whereColumn('category_product.product_id', 'products.id')->whereNull('categories.deleted_at');
        $categoryAggregate = match (DB::getDriverName()) {
            'pgsql' => "STRING_AGG(categories.name, ', ' ORDER BY categories.name)",
            'mysql', 'mariadb' => "GROUP_CONCAT(categories.name ORDER BY categories.name SEPARATOR ', ')",
            default => "GROUP_CONCAT(categories.name, ', ')",
        };
        $days = match (DB::getDriverName()) {
            'pgsql' => 'stock.expires_on - CAST(? AS DATE)',
            'mysql', 'mariadb' => 'DATEDIFF(stock.expires_on, ?)',
            default => 'CAST(JULIANDAY(stock.expires_on) - JULIANDAY(?) AS INTEGER)',
        };
        $stockValue = '(COALESCE(stock.valued_stock, 0) + COALESCE(stock.unpriced_stock, 0) * (products.cost_price_cents / 1000.0))';
        $unitCost = "COALESCE($stockValue * 1000 / NULLIF(stock.balance, 0), products.cost_price_cents) / 100.0";
        $products = Product::query()->toBase()
            ->join('unit_of_measures', 'unit_of_measures.id', '=', 'products.unit_of_measure_id')
            ->leftJoinSub($batches, 'stock', 'stock.product_id', 'products.id')
            ->select('products.id', 'products.sku', 'products.name', 'products.description', 'unit_of_measures.code as unit', 'stock.expires_on')
            ->selectSub($category->selectRaw($categoryAggregate), 'category')
            ->selectRaw("$days AS days_remaining", [$today])
            ->selectRaw('COALESCE(stock.balance, 0) / 1000.0 AS quantity, products.minimum_stock_milliunits / 1000.0 AS minimum_stock')
            ->selectRaw("ROUND($unitCost, 2) AS average_cost, ROUND($stockValue, 0) / 100.0 AS current_value")
            ->selectRaw("CASE
                WHEN NOT products.is_active THEN 'inactive'
                WHEN COALESCE(stock.balance, 0) = 0 THEN 'out_of_stock'
                WHEN stock.expired = 1 THEN 'expired'
                WHEN stock.expiring = 1 THEN 'expiring'
                WHEN stock.balance <= products.minimum_stock_milliunits THEN 'critical'
                ELSE 'in_stock' END AS status")
            ->selectRaw('COALESCE(stock.expired, 0) AS expired, COALESCE(stock.expiring, 0) AS expiring');

        return DB::query()->fromSub($products, 'inventory');
    }
}
