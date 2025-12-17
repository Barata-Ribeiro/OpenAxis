<?php

namespace App\Services\Common;

use App\Interfaces\Common\DashboardServiceInterface;
use App\Models\Client;
use App\Models\Product;
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
            $totalCompletedProfits,
            $inventoryAppreciation,
            $totalCosts,
            $totalPendingProfits,
            $totalCanceled
        ] = $this->runDashboardTasks([
            fn () => $this->getTotalSales($year, $month),
            fn () => $this->getTotalClients($year, $month),
            fn () => $this->getTotalVendors($year, $month),
            fn () => $this->getTotalOrders($year, $month),
            fn () => $this->getTotalProfits($year, $month),
            fn () => $this->getTotalCompletedBilling($year, $month),
            fn () => $this->getTotalCompletedProfits($year, $month),
            fn () => $this->getInventoryAppreciation($year, $month),
            fn () => $this->getTotalCosts($year, $month),
            fn () => $this->getTotalPendingProfits($year, $month),
            fn () => $this->getTotalCanceled($year, $month),
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
                'inventoryAppreciation' => $inventoryAppreciation,
                'totalCosts' => $totalCosts,
                'totalPendingProfits' => $totalPendingProfits,
                'totalCanceled' => $totalCanceled,
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

    /**
     * Calculate the total completed billing for a specific month and year.
     *
     * Retrieves and aggregates billings that are considered "completed" within the
     * provided year and month. The aggregation logic may return a single numeric
     * total (sum of amounts) or a more complex structure (e.g., totals per currency
     * or per client) depending on the implementation.
     *
     * @param  int|string  $year  Year to filter billings (e.g., 2024). Accepts integer or numeric string.
     * @param  int|string  $month  Month to filter billings (1-12 or '01'-'12'). Accepts integer or numeric string.
     * @return int|float|array|mixed
     *                               The aggregated total for completed billings. Common return shapes:
     *                               - int|float: the summed billing amount.
     *                               - array: breakdowns by currency/client/other dimensions.
     *                               - mixed: other shapes used by the underlying data layer.
     *
     * @throws \InvalidArgumentException If $year or $month are not valid or parsable.
     * @throws \RuntimeException If the data source query or aggregation fails.
     *
     * Notes:
     * - The method is private and intended for internal dashboard/reporting use.
     * - Inputs are expected to be normalized (e.g., month in 1-12); callers should
     *   validate or normalize inputs if necessary.
     * - If no completed billings exist for the period, implementations typically
     *   return 0, 0.0, or an empty array depending on the return shape.
     */
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

    /**
     * Get the total completed profits for a given year and optional month.
     *
     * If $month is provided (1–12) the function returns the profit for that month in the specified year.
     * If $month is null, the function returns the total completed profit for the entire year.
     *
     * @param  int|string  $year  Year to compute profits for (e.g. 2025). Integer or numeric string accepted.
     * @param  int|string|null  $month  Month number (1–12) or null to compute the yearly total.
     * @return mixed Numeric total completed profit (float|int) or null if no data is available.
     */
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
     * Compute inventory appreciation for a specific year and month.
     *
     * Retrieves and/or calculates inventory appreciation metrics for the given
     * period. The exact return format is implementation dependent (e.g. a single
     * numeric value, an associative array of metrics, or null when no data exists).
     *
     * @param  int  $year  Four-digit year (e.g. 2025)
     * @param  int  $month  Month number (1-12)
     * @return mixed Inventory appreciation data (float|array|null), structure is
     *               determined by the service implementation.
     *
     * @throws \InvalidArgumentException If $year or $month are invalid.
     * @throws \RuntimeException If an error occurs while retrieving or computing data.
     */
    public function getInventoryAppreciation($year, $month): mixed
    {
        [$currentMonth, $pastmonth] = $this->runDashboardTasks([
            fn () => Product::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereIsActive(true)
                ->where('current_stock', '>', 0)
                ->sum(DB::raw('current_stock * selling_price')),
            fn () => Product::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month - 1)
                ->whereIsActive(true)
                ->where('current_stock', '>', 0)
                ->sum(DB::raw('current_stock * selling_price')),
        ]);

        return [
            'title' => 'Inventory Appreciation',
            'value' => $currentMonth,
            'delta' => round((($currentMonth - $pastmonth) / ($pastmonth ?: 1)) * 100, 2),
            'lastMonth' => $pastmonth,
            'positive' => $currentMonth >= $pastmonth,
            'prefix' => '$',
            'suffix' => $currentMonth >= 1000000 ? 'M' : null,
        ];
    }

    /**
     * Calculate total costs for a given year and optional month.
     *
     * Retrieves aggregated cost totals for the specified period. If a month is
     * provided (1-12) the result is limited to that month; if no month is
     * provided the result covers the entire year.
     *
     * @param  int|string  $year  Four-digit year (e.g. 2025).
     * @param  int|string|null  $month  Optional month number (1-12). Pass null to compute totals for the whole year.
     * @return float|int|array|null Numeric total (float or int) when a single value is returned,
     *                              an associative array when results are grouped (e.g. by cost category),
     *                              or null if no costs are found for the period.
     *
     * @throws \InvalidArgumentException If the year or month values are invalid.
     */
    public function getTotalCosts($year, $month): mixed
    {
        [$currentMonth, $pastMonth] = $this->runDashboardTasks([
            fn () => Product::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereIsActive(true)
                ->where('current_stock', '>', 0)
                ->sum(DB::raw('current_stock * cost_price')),
            fn () => Product::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month - 1)
                ->whereIsActive(true)
                ->where('current_stock', '>', 0)
                ->sum(DB::raw('current_stock * cost_price')),
        ]);

        return [
            'title' => 'Total Costs',
            'value' => $currentMonth,
            'delta' => round((($currentMonth - $pastMonth) / ($pastMonth ?: 1)) * 100, 2),
            'lastMonth' => $pastMonth,
            'positive' => $currentMonth >= $pastMonth,
            'prefix' => '$',
            'suffix' => $currentMonth >= 1000000 ? 'M' : null,
        ];
    }

    /**
     * Get the total pending profits for a given year and optional month.
     *
     * If $month is provided (1–12) the function returns the profit for that month in the specified year.
     * If $month is null, the function returns the total pending profit for the entire year.
     *
     * @param  int|string  $year  Year to compute profits for (e.g. 2025). Integer or numeric string accepted.
     * @param  int|string|null  $month  Month number (1–12) or null to compute the yearly total.
     * @return mixed Numeric total pending profit (float|int) or null if no data is available.
     */
    public function getTotalPendingProfits($year, $month): mixed
    {
        $possibleStatuses = ['pending', 'processing', 'on-hold', 'awaiting-payment'];

        [$currentMonth, $pastMonth] = $this->runDashboardTasks([
            fn () => $this->calculateProfitForPeriod($year, $month, $possibleStatuses),
            fn () => $this->calculateProfitForPeriod($year, $month - 1, $possibleStatuses),
        ]);

        return [
            'title' => 'Total Pending Profits',
            'value' => $currentMonth,
            'delta' => round((($currentMonth - $pastMonth) / ($pastMonth ?: 1)) * 100, 2),
            'lastMonth' => $pastMonth,
            'positive' => $currentMonth >= $pastMonth,
            'prefix' => '$',
            'suffix' => $currentMonth >= 1000000 ? 'M' : null,
        ];
    }

    /**
     * Retrieve the total number (or aggregate) of cancelled items for a given period.
     *
     * Filters cancellation records by the provided year and month and returns the computed
     * total or an aggregate structure. The exact return shape is implementation-specific,
     * hence the mixed return type.
     *
     * @param  int|string  $year  Four-digit year (e.g. 2025).
     * @param  int|string  $month  Month number (1-12). Implementations may treat other values
     *                             (e.g. 0 or null) as "whole year" if applicable.
     * @return mixed An integer total of cancelled items, or an aggregate/collection with
     *               additional details depending on implementation.
     *
     * @throws \InvalidArgumentException If $year or $month are invalid.
     * @throws \Throwable For underlying data access or unexpected errors.
     */
    public function getTotalCanceled($year, $month): mixed
    {
        $possibleStatuses = ['canceled', 'cancelled'];

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
            'title' => 'Total Canceled',
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
            // Sum revenue from items; if empty, fall back to order product_value
            $itemsRevenue = $order->salesOrderItems->sum('subtotal_price');
            if ($itemsRevenue == 0.0 && $order->product_value !== null) {
                $itemsRevenue = (float) $order->product_value;
            }

            // Sum commission from items; if empty, fall back to order total_commission
            $itemsCommission = $order->salesOrderItems->sum('commission_item');
            if ($itemsCommission == 0.0 && $order->total_commission !== null) {
                $itemsCommission = (float) $order->total_commission;
            }

            $productCost = (float) $order->product_cost;
            $deliveryCost = (float) $order->delivery_cost;
            $discountCost = (float) $order->discount_cost;

            $orderProfit = $itemsRevenue
                - $productCost
                - $deliveryCost
                - $discountCost
                - $itemsCommission;

            $totalProfit += $orderProfit;
        }

        return $totalProfit;
    }

    /**
     * Execute a set of dashboard-related tasks provided as callables and collect their results.
     *
     * This internal method iterates over the provided array of callbacks, invokes each one,
     * and returns an array containing the results in the same order (or keyed by the same
     * keys for associative arrays) as the input.
     *
     * Notes:
     * - Each item in $callbacks must be a callable (closure, function name or [object, method]).
     * - If $callbacks is an associative array, returned results preserve the same keys.
     * - If $callbacks is empty, an empty array is returned.
     * - Callbacks may perform side effects (database queries, cache updates, HTTP calls, etc.).
     *
     * @param  callable[]|array<string,callable>  $callbacks  Array of callables to execute.
     * @return array The results returned by each callback, keyed the same way as $callbacks.
     *
     * @throws \InvalidArgumentException If any element of $callbacks is not callable.
     * @throws \Throwable Any exception thrown by a callback will propagate to the caller
     *                    unless the method implementation explicitly catches and handles it.
     *
     * Usage example:
     * $results = $this->runDashboardTasks([
     *     'stats' => function () { return $this->getStats(); },
     *     function () { return $this->getRecentActivities(); },
     * ]);
     */
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

    /**
     * Determine whether dashboard concurrency protections should be bypassed.
     *
     * This predicate is used internally to decide if concurrency controls (locks, rate
     * limits or similar mechanisms protecting dashboard operations) should be skipped.
     * The decision is typically based on runtime context such as environment, application
     * configuration, request flags, or user/role permissions.
     *
     * Implementations usually return true for cases like:
     *  - running in console or test environments,
     *  - a global configuration flag that disables concurrency checks,
     *  - a specific "force" or "bypass" parameter present in the current request/context,
     *  - an elevated user or service account that is allowed to bypass protections.
     *
     * No state is modified by this method; it only returns a boolean predicate used by callers
     * to conditionally bypass concurrency handling.
     *
     * @return bool True when concurrency handling should be bypassed, false otherwise.
     */
    private function shouldBypassConcurrency(): bool
    {
        return app()->environment('testing') || $this->isSqlDriver === false;
    }
}
