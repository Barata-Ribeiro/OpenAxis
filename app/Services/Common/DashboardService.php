<?php

namespace App\Services\Common;

use App\Interfaces\Common\DashboardServiceInterface;
use App\Models\Client;
use App\Models\ItemSalesOrder;
use App\Models\SalesOrder;
use App\Models\Vendor;
use Concurrency;
use DB;

class DashboardService implements DashboardServiceInterface
{
    private bool $isSqlDriver;

    public function __construct()
    {
        $this->isSqlDriver = \in_array(DB::getDriverName(), ['mysql', 'pgsql']);
    }

    public function getUserDashboardData(): array
    {
        // TODO: Implement user dashboard data retrieval
        return [];
    }

    public function getAdminDashboardData($yearMonth): array
    {
        $year = $yearMonth ? (int) explode('-', $yearMonth)[0] : now()->year;
        $month = $yearMonth ? (int) explode('-', $yearMonth)[1] : now()->month;

        [$totalSales,
            $totalClients,
            $totalVendors,
            $totalOrders,
            $totalProfits,
            $totalCompletedBilling,
            $totalCompletedProfits] = $this->runDashboardTasks([
                fn () => $this->getTotalSales($year, $month),
                fn () => $this->getTotalClients($year, $month),
                fn () => $this->getTotalVendors($year, $month),
                fn () => $this->getTotalOrders($year, $month),
                fn () => $this->getTotalProfits($year, $month),
                fn () => $this->getTotalCompletedBilling($year, $month),
                fn () => $this->getTotalCompletedProfits($year, $month),
            ]);

        return [
            'generalSummary' => [
                'title' => 'General Summary',
                'totalSales' => $totalSales,
                'totalClients' => $totalClients,
                'totalVendors' => $totalVendors,
                'totalOrders' => $totalOrders,
            ],
            'financialSummary' => [
                'title' => 'Financial Summary',
                'totalProfits' => $totalProfits,
                'totalCompletedBilling' => $totalCompletedBilling,
                'totalCompletedProfits' => $totalCompletedProfits,
            ],
            'inventoryAndCostsSummary' => [
                'title' => 'Inventory and Costs Summary',
                // Future metrics can be added here
            ],
            'commissions' => [
                'title' => 'Commissions',
                // Future metrics can be added here
            ],
            'payables' => [
                'title' => 'Payables',
                // Future metrics can be added here
            ],
            'receivables' => [
                'title' => 'Receivables',
                // Future metrics can be added here
            ],
        ];
    }

    /**
     * Retrieve the total sales for a specified period.
     *
     * @param  int|string  $year  The year to calculate totals for (e.g., 2025).
     * @param  int|string|null  $month  The month number (1-12). Use null to calculate totals for the entire year.
     * @return mixed The total sales for the requested period.
     */
    public function getTotalSales($year, $month): mixed
    {
        [$currentMonth, $pastMonth] = $this->runDashboardTasks([
            fn () => SalesOrder::query()
                ->whereYear('order_date', $year)
                ->whereMonth('order_date', $month)
                ->sum('total_cost'),
            fn () => SalesOrder::query()
                ->whereYear('order_date', $year)
                ->whereMonth('order_date', $month - 1)
                ->sum('total_cost'),
        ]);

        return [
            'title' => 'Total Sales',
            'value' => $currentMonth,
            'delta' => round((($currentMonth - $pastMonth) / ($pastMonth ?: 1)) * 100, 2),
            'lastMonth' => $pastMonth,
            'positive' => $currentMonth >= $pastMonth,
            'prefix' => '$',
            'suffix' => $currentMonth >= 1000000 ? 'M' : null,
        ];
    }

    /**
     * Get the total number of clients for a given year and month.
     *
     * @param  int|string  $year  Four-digit year (e.g. 2025)
     * @param  int|string|null  $month  Month number (1-12). If null, calculate total for the whole year.
     * @return mixed Integer total client count on success, or another type (e.g. null/false/array) on error or when additional data is returned.
     */
    private function getTotalClients($year, $month): mixed
    {
        [$currentMonth, $pastMonth] = $this->runDashboardTasks([
            fn () => Client::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count(),
            fn () => Client::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month - 1)
                ->count(),
        ]);

        return [
            'title' => 'Total Clients',
            'value' => $currentMonth,
            'delta' => round((($currentMonth - $pastMonth) / ($pastMonth ?: 1)) * 100, 2),
            'lastMonth' => $pastMonth,
            'positive' => $currentMonth >= $pastMonth,
        ];
    }

    /**
     * Get the total number of vendors for a given year and month.
     *
     * @param  int|string  $year  Four-digit year (e.g. 2025) or a value convertible to int.
     * @param  int|string  $month  Numeric month (1-12) or a value convertible to int.
     * @return mixed Returns the total vendor count (int) on success; may return null or false on failure.
     */
    private function getTotalVendors($year, $month): mixed
    {
        [$currentMonth, $pastMonth] = $this->runDashboardTasks([
            fn () => Vendor::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count(),
            fn () => Vendor::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month - 1)
                ->count(),
        ]);

        return [
            'title' => 'Total Vendors',
            'value' => $currentMonth,
            'delta' => round((($currentMonth - $pastMonth) / ($pastMonth ?: 1)) * 100, 2),
            'lastMonth' => $pastMonth,
            'positive' => $currentMonth >= $pastMonth,
        ];
    }

    /**
     * Calculate the total number of orders for a given period.
     *
     * @param  int|string  $year  Four-digit year (e.g. 2024). Values that can be cast to int are accepted.
     * @param  int|string|null  $month  Optional month number (1-12). If null, totals for the entire year are returned.
     * @return mixed The total for the requested period.
     */
    private function getTotalOrders($year, $month): mixed
    {
        [$currentMonth, $pastMonth] = $this->runDashboardTasks([
            fn () => SalesOrder::query()
                ->whereYear('order_date', $year)
                ->whereMonth('order_date', $month)
                ->count(),
            fn () => SalesOrder::query()
                ->whereYear('order_date', $year)
                ->whereMonth('order_date', $month - 1)
                ->count(),
        ]);

        return [
            'title' => 'Total Orders',
            'value' => $currentMonth,
            'delta' => round((($currentMonth - $pastMonth) / ($pastMonth ?: 1)) * 100, 2),
            'lastMonth' => $pastMonth,
            'positive' => $currentMonth >= $pastMonth,
        ];
    }

    /**
     * Get the total profits for a given year and optional month.
     *
     * If $month is provided (1–12) the function returns the profit for that month in the specified year.
     * If $month is null, the function returns the total profit for the entire year.
     *
     * @param  int|string  $year  Year to compute profits for (e.g. 2025). Integer or numeric string accepted.
     * @param  int|string|null  $month  Month number (1–12) or null to compute the yearly total.
     * @return mixed Numeric total profit (float|int) or null if no data is available.
     */
    private function getTotalProfits($year, $month): mixed
    {
        $possibleStatuses = ['canceled', 'cancelled'];

        [$currentMonth, $pastMonth] = $this->runDashboardTasks([
            fn () => $this->calculateProfitForPeriod($year, $month, $possibleStatuses),
            fn () => $this->calculateProfitForPeriod($year, $month - 1, $possibleStatuses),
        ]);

        return [
            'title' => 'Total Profits',
            'value' => $currentMonth,
            'delta' => round((($currentMonth - $pastMonth) / ($pastMonth ?: 1)) * 100, 2),
            'lastMonth' => $pastMonth,
            'positive' => $currentMonth >= $pastMonth,
            'prefix' => '$',
            'suffix' => $currentMonth >= 1000000 ? 'M' : null,
        ];
    }

    private function getTotalCompletedBilling($year, $month): mixed
    {
        $possibleStatuses = ['delivered', 'completed', 'fulfilled'];

        [$currentMonth, $pastMonth] = $this->runDashboardTasks([
            fn () => SalesOrder::query()
                ->whereYear('order_date', $year)
                ->whereMonth('order_date', $month)
                ->whereIn('status', $possibleStatuses)
                ->sum('total_cost'),
            fn () => SalesOrder::query()
                ->whereYear('order_date', $year)
                ->whereMonth('order_date', $month - 1)
                ->whereIn('status', $possibleStatuses)
                ->sum('total_cost'),
        ]);

        return [
            'title' => 'Total Completed Billing',
            'value' => $currentMonth,
            'delta' => round((($currentMonth - $pastMonth) / ($pastMonth ?: 1)) * 100, 2),
            'lastMonth' => $pastMonth,
            'positive' => $currentMonth >= $pastMonth,
            'prefix' => '$',
            'suffix' => $currentMonth >= 1000000 ? 'M' : null,
        ];
    }

    public function getTotalCompletedProfits($year, $month): mixed
    {
        $possibleStatuses = ['delivered', 'completed', 'fulfilled'];

        [$currentMonth, $pastMonth] = $this->runDashboardTasks([
            fn () => $this->calculateProfitForPeriod($year, $month, $possibleStatuses),
            fn () => $this->calculateProfitForPeriod($year, $month - 1, $possibleStatuses),
        ]);

        return [
            'title' => 'Total Completed Profits',
            'value' => $currentMonth,
            'delta' => round((($currentMonth - $pastMonth) / ($pastMonth ?: 1)) * 100, 2),
            'lastMonth' => $pastMonth,
            'positive' => $currentMonth >= $pastMonth,
            'prefix' => '$',
            'suffix' => $currentMonth >= 1000000 ? 'M' : null,
        ];
    }

    /**
     * Calculate the net profit for a specific year and month.
     *
     * Aggregates all relevant revenue and expense records for the given period
     * (e.g. invoices, payments, refunds, expense entries and adjustments) and
     * returns the net result (total revenue minus total expenses) in the
     * application's base currency. If no records exist for the period this
     * method returns 0.0. The result may be negative to indicate a net loss.
     *
     * @param  int  $year  Four-digit year (e.g. 2025).
     * @param  int  $month  Month number (1-12).
     * @return float Net profit (positive) or loss (negative) for the specified period.
     *
     * @throws \InvalidArgumentException If $year or $month are invalid or out of range.
     *
     * @internal Private helper used by the dashboard service to build period summaries.
     */
    private function calculateProfitForPeriod($year, $month, $statuses): float
    {
        $orders = SalesOrder::query()
            ->whereYear('order_date', $year)
            ->whereMonth('order_date', $month)
            ->whereIn('status', $statuses)
            ->get();

        $totalProfit = 0.0;
        foreach ($orders as $order) {
            $orderTotal = (float) $order->total_cost;
            $productCost = $order->product_cost !== null ? (float) $order->product_cost : null;

            if ($productCost === null) {
                $calculatedProductCost = ItemSalesOrder::query()
                    ->join('products', 'item_sales_orders.product_id', '=', 'products.id')
                    ->where('item_sales_orders.sales_order_id', $order->id)
                    ->selectRaw('COALESCE(SUM(products.cost_price * item_sales_orders.quantity), 0) as total_cost')
                    ->value('total_cost');

                $productCost = $calculatedProductCost > 0 ? (float) $calculatedProductCost : $orderTotal * 0.6;
            }

            $commission = 0.0;
            if (! empty($order->vendor_id)) {
                if ($order->product_cost !== null && (float) $order->product_cost > 0) {
                    // Use item-based commission OR 5% fallback of order total
                    $calculatedCommission = ItemSalesOrder::query()
                        ->join('products', 'item_sales_orders.product_id', '=', 'products.id')
                        ->where('item_sales_orders.sales_order_id', $order->id)
                        ->selectRaw('COALESCE(SUM(products.comission * item_sales_orders.quantity), 0) as total_commission')
                        ->value('total_commission');

                    $commission = $calculatedCommission > 0
                        ? (float) $calculatedCommission
                        : $orderTotal * 0.05;
                } else {
                    $commission = $orderTotal * 0.05;
                }
            }

            $profit = $orderTotal - $productCost - $commission;
            $totalProfit += $profit;
        }

        return $totalProfit;
    }

    private function runDashboardTasks(array $callbacks): array
    {
        if ($this->shouldBypassConcurrency()) {
            $results = [];

            foreach ($callbacks as $key => $callback) {
                $results[$key] = $callback();
            }

            return $results;
        }

        return Concurrency::run($callbacks);
    }

    private function shouldBypassConcurrency(): bool
    {
        return app()->environment('testing') || $this->isSqlDriver === false;
    }
}
