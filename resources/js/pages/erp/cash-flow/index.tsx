import DataTable, { type ServerTableData } from '@/components/data-table/data-table';
import { ErpDate, ErpMoney, useErpFormatting } from '@/components/erp/format';
import { actionColumn, choices, ErpLink, ErpPage, humanize, Metrics, tableColumn } from '@/components/erp/page';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { RangeCalendar } from '@/components/ui/calendar';
import { Popover, PopoverTrigger } from '@/components/ui/popover';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { usePermission } from '@/hooks/use-permission';
import { cn } from '@/lib/utils';
import { dashboard } from '@/routes';
import cash from '@/routes/erp/cash-flow';
import payables from '@/routes/erp/cash-flow/payables';
import { router } from '@inertiajs/react';
import { differenceInCalendarDays, parseISO } from 'date-fns';
import { CalendarDays } from 'lucide-react';
import { useState } from 'react';

type CashRow = {
    id: number;
    occurred_at: string;
    description: string;
    type: string;
    payment: string;
    category: string;
    amount: string;
    due_on: string;
    outstanding_cents: number;
    status: string;
};

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

const column = tableColumn<CashRow>;
const dateColumn = column('occurred_at', 'Date and time', 'date', (row) => (
    <ErpDate value={row.occurred_at} withTime />
));
const descriptionColumn = column('description', 'Description');
const categoryColumn = column('category', 'Category');
const amountColumn = column('amount', 'Amount', 'number', (row) => <ErpMoney amount={Number(row.amount)} />);
const entryActions = actionColumn<CashRow>((row) => (
    <ErpLink href={cash.showEntry.url(row.id)} permission="cash-entry.show" compact modal>
        View
    </ErpLink>
));

function DueDate({ value, today }: Readonly<{ value: string; today: string }>) {
    const days = differenceInCalendarDays(parseISO(value), parseISO(today));
    const dueStatusMessage = days < 0 ? `${-days}d overdue` : `In ${days}d`;

    return (
        <div className="flex flex-col">
            <ErpDate value={value} />
            <span className={cn('text-xs text-muted-foreground', days < 0 && 'text-destructive')}>
                {days === 0 ? 'Due today' : dueStatusMessage}
            </span>
        </div>
    );
}

function PayableActions({ row }: Readonly<{ row: CashRow }>) {
    const { can } = usePermission();
    return (
        <>
            <ErpLink href={payables.show.url(row.id)} permission="financial-account.show" compact modal>
                View
            </ErpLink>
            {row.status !== 'cancelled' && (
                <ErpLink href={payables.edit.url(row.id)} permission="financial-account.edit" compact modal>
                    Edit
                </ErpLink>
            )}
            {Number(row.outstanding_cents) > 0 && can('financial-account.show') && (
                <ErpLink href={payables.confirmPayment.url(row.id)} permission="cash-entry.create" compact modal>
                    Confirm payment
                </ErpLink>
            )}
            {Number(row.outstanding_cents) === Math.round(Number(row.amount) * 100) && (
                <ErpLink href={payables.cancel.url(row.id)} permission="financial-account.cancel" compact modal>
                    Cancel
                </ErpLink>
            )}
        </>
    );
}

const receiptColumns = [
    dateColumn,
    column('type', 'Type', 'select', (row) => humanize(row.type), choices(['manual', 'settlement'])),
    descriptionColumn,
    column('payment', 'Payment method'),
    amountColumn,
    entryActions,
];
const expenseColumns = [
    column('occurred_at', 'Date', 'date', (row) => <ErpDate value={row.occurred_at} />),
    descriptionColumn,
    categoryColumn,
    amountColumn,
    entryActions,
];

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
    table: ServerTableData<CashRow> | null;
    period: Period;
    tab: Tab;
    tabs: Tab[];
}>) {
    const [calendarOpen, setCalendarOpen] = useState(false);
    const { money: currency } = useErpFormatting();
    const money = (cents: number | null) => (cents === null ? null : currency.format(cents / 100));
    const change = (next: Period, target = tab) => {
        router.get(
            cash.index.url({ query: rangeQuery(next, target) }),
            {},
            { preserveScroll: true, viewTransition: false },
        );
    };
    const payableColumns = [
        descriptionColumn,
        categoryColumn,
        amountColumn,
        column('due_on', 'Due date', 'date', (row) => <DueDate value={row.due_on} today={period.today} />),
        column(
            'status',
            'Status',
            'select',
            (row) => (
                <Badge variant={row.status === 'overdue' ? 'destructive' : 'secondary'}>{humanize(row.status)}</Badge>
            ),
            choices(['pending', 'partially_paid', 'paid', 'overdue', 'cancelled']),
        ),
        actionColumn<CashRow>((row) => <PayableActions row={row} />),
    ];
    const transactionColumns = tab === 'expenses' ? expenseColumns : payableColumns;
    const columns = tab === 'receipts' ? receiptColumns : transactionColumns;
    const negativeExpenseTotal = tab === 'expenses' && summary.expenses !== null ? -summary.expenses : null;
    const total = tab === 'receipts' ? summary.receipts : negativeExpenseTotal;
    const url = cash.index.url({ query: rangeQuery(period, tab) });
    const totalPositiveClass = total && total > 0 ? 'text-emerald-700 dark:text-emerald-400' : '';

    return (
        <ErpPage
            title="Cash flow"
            actions={
                <>
                    <ErpLink href={cash.createReceipt.url()} permission="cash-entry.create" modal>
                        Manual receipt
                    </ErpLink>
                    <ErpLink href={payables.create.url()} permission="financial-account.create" modal>
                        New payable
                    </ErpLink>
                    <ErpLink href={cash.createExpense.url()} permission="cash-entry.create" modal>
                        New expense
                    </ErpLink>
                </>
            }
        >
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
                <TabsList aria-label="Cash flow sections" className="max-w-full justify-start overflow-x-auto">
                    {tabs.map((item) => (
                        <TabsTrigger key={item} id={item} className="px-3">
                            {tabLabels[item]}
                        </TabsTrigger>
                    ))}
                </TabsList>
                <TabsContent id={tab} className="min-w-0 space-y-3">
                    {table && <DataTable key={url} columns={columns} result={table} url={url} label={tabLabels[tab]} />}
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
        </ErpPage>
    );
}

CashFlow.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Cash flow', href: cash.index() },
    ],
};
