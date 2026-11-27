import type { ServerTableData } from '@/components/data-table/data-table';
import DataTable from '@/components/data-table/data-table';
import type { ExpenseRow } from '@/components/erp/cash-flow/entries/expense-columns';
import { columns as expenseColumns } from '@/components/erp/cash-flow/entries/expense-columns';
import type { ReceiptRow } from '@/components/erp/cash-flow/entries/receipt-columns';
import { columns as receiptColumns } from '@/components/erp/cash-flow/entries/receipt-columns';
import type { PayableRow } from '@/components/erp/cash-flow/payables/columns';
import { columns as payableColumns } from '@/components/erp/cash-flow/payables/columns';
import { ErpDate } from '@/components/erp/format';
import { Metrics } from '@/components/erp/metrics';
import { Button, buttonVariants } from '@/components/ui/button';
import { RangeCalendar } from '@/components/ui/calendar';
import { Popover, PopoverTrigger } from '@/components/ui/popover';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useErpFormatting } from '@/hooks/erp/use-erp-formatting';
import { usePermission } from '@/hooks/use-permission';
import { cn } from '@/lib/utils';
import { dashboard } from '@/routes';
import cash from '@/routes/erp/cash-flow';
import payables from '@/routes/erp/cash-flow/payables';
import { Head, router } from '@inertiajs/react';
import { ModalLink } from '@inertiaui/modal-react';
import { CalendarDays } from 'lucide-react';
import { Activity, useState } from 'react';

const presets = { today: 'Today', week: 'This week', month: 'This month', custom: 'Custom' } as const;
const tabLabels = {
    summary: 'Summary',
    receipts: 'Receipts',
    expenses: 'Expenses',
    payables: 'Accounts payable',
} as const;

type Preset = keyof typeof presets;
type Tab = keyof typeof tabLabels;
type Period = { preset: Preset; start: string; end: string; today: string };
type Summary = Record<
    'receipts' | 'expenses' | 'balance' | 'payables' | 'net_profit' | 'receipt_count' | 'expense_count',
    number | null
>;

type CashFlowTable =
    | { tab: 'summary'; table: null }
    | { tab: 'receipts'; table: ServerTableData<ReceiptRow> }
    | { tab: 'expenses'; table: ServerTableData<ExpenseRow> }
    | { tab: 'payables'; table: ServerTableData<PayableRow> };

function rangeQuery(period: Period, tab: Tab) {
    return {
        tab,
        period: period.preset,
        start: period.preset === 'custom' ? period.start : undefined,
        end: period.preset === 'custom' ? period.end : undefined,
    };
}

export default function CashFlow({
    summary,
    table,
    period,
    tab,
    tabs,
}: Readonly<{
    summary: Summary;
    period: Period;
    tabs: Tab[];
}> &
    CashFlowTable) {
    const [calendarOpen, setCalendarOpen] = useState(false);
    const { money: currency } = useErpFormatting();
    const { can } = usePermission();
    const money = (cents: number | null) => (cents === null ? null : currency.format(cents / 100));
    const change = (next: Period, target = tab) => {
        router.get(
            cash.index.url({ query: rangeQuery(next, target) }),
            {},
            { preserveScroll: true, viewTransition: false },
        );
    };
    const negativeExpenseTotal = tab === 'expenses' && summary.expenses !== null ? -summary.expenses : null;
    const total = tab === 'receipts' ? summary.receipts : negativeExpenseTotal;
    const url = cash.index.url({ query: rangeQuery(period, tab) });
    const totalPositiveClass = total && total > 0 ? 'text-emerald-700 dark:text-emerald-400' : '';

    return (
        <section className="flex min-w-0 flex-col gap-5 p-4 sm:p-6">
            <Head title="Cash flow" />
            <header className="flex flex-wrap items-center justify-between gap-3">
                <h1 className="text-2xl font-semibold tracking-tight">Cash flow</h1>
                <div className="flex flex-wrap gap-2">
                    <Activity mode={can('cash-entry.create') ? 'visible' : 'hidden'}>
                        <ModalLink
                            href={cash.createReceipt.url()}
                            className={buttonVariants({ variant: 'outline' })}
                            navigate
                        >
                            Manual receipt
                        </ModalLink>
                    </Activity>
                    <Activity mode={can('financial-account.create') ? 'visible' : 'hidden'}>
                        <ModalLink
                            href={payables.create.url()}
                            className={buttonVariants({ variant: 'outline' })}
                            navigate
                        >
                            New payable
                        </ModalLink>
                    </Activity>
                    <Activity mode={can('cash-entry.create') ? 'visible' : 'hidden'}>
                        <ModalLink
                            href={cash.createExpense.url()}
                            className={buttonVariants({ variant: 'outline' })}
                            navigate
                        >
                            New expense
                        </ModalLink>
                    </Activity>
                </div>
            </header>
            <div className="flex flex-wrap items-center gap-2" role="group" aria-label="Reporting period">
                {(['today', 'week', 'month'] as const).map((preset) => (
                    <Button
                        key={preset}
                        size="sm"
                        variant={period.preset === preset ? 'default' : 'outline'}
                        aria-pressed={period.preset === preset}
                        onPress={() => change({ ...period, preset })}
                    >
                        {presets[preset]}
                    </Button>
                ))}
                <PopoverTrigger isOpen={calendarOpen} onOpenChange={setCalendarOpen}>
                    <Button size="sm" variant={period.preset === 'custom' ? 'default' : 'outline'}>
                        <CalendarDays aria-hidden /> Custom
                    </Button>
                    <Popover className="w-auto" placement="bottom start">
                        <RangeCalendar
                            aria-label="Custom cash flow period"
                            onChange={(range) => {
                                setCalendarOpen(false);
                                change({
                                    ...period,
                                    preset: 'custom',
                                    start: range.start.toString(),
                                    end: range.end.toString(),
                                });
                            }}
                        />
                    </Popover>
                </PopoverTrigger>
                <p className="text-sm text-muted-foreground" aria-live="polite">
                    <ErpDate value={period.start} />
                    {period.end !== period.start && (
                        <>
                            {' '}
                            – <ErpDate value={period.end} />
                        </>
                    )}
                </p>
            </div>
            <Metrics
                items={[
                    { title: 'Receipts', value: money(summary.receipts) },
                    { title: 'Expenses', value: money(summary.expenses) },
                    { title: 'Balance', value: money(summary.balance) },
                    { title: 'Projected payables', value: money(summary.payables) },
                    { title: 'Net profit', value: money(summary.net_profit) },
                ]}
            />
            <Tabs selectedKey={tab} keyboardActivation="manual" onSelectionChange={(key) => change(period, key as Tab)}>
                <TabsList aria-label="Cash flow sections">
                    {tabs.map((item) => (
                        <TabsTrigger key={item} id={item} className="px-3">
                            {tabLabels[item]}
                        </TabsTrigger>
                    ))}
                </TabsList>

                <TabsContent id={tab}>
                    {tab === 'receipts' && (
                        <DataTable key={url} columns={receiptColumns} result={table} url={url} label={tabLabels[tab]} />
                    )}

                    {tab === 'expenses' && (
                        <DataTable key={url} columns={expenseColumns} result={table} url={url} label={tabLabels[tab]} />
                    )}

                    {tab === 'payables' && (
                        <DataTable key={url} columns={payableColumns} result={table} url={url} label={tabLabels[tab]} />
                    )}

                    {total !== null && (
                        <div
                            className={cn(
                                'flex justify-between rounded-lg border px-4 py-3 font-semibold tabular-nums',
                                total < 0 ? 'text-destructive' : totalPositiveClass,
                            )}
                        >
                            <span>Period total</span>
                            <span>{money(total)}</span>
                        </div>
                    )}
                </TabsContent>
            </Tabs>
        </section>
    );
}

CashFlow.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Cash flow', href: cash.index() },
    ],
};
