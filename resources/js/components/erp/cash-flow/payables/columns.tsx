import { ErpDate, ErpMoney } from '@/components/erp/format';
import { Badge } from '@/components/ui/badge';
import { createAppColumnHelper } from '@/hooks/data-table/table';
import { choices, humanize } from '@/lib/erp/format';
import { cn } from '@/lib/utils';
import payables from '@/routes/erp/cash-flow/payables';
import { usePage } from '@inertiajs/react';
import { differenceInCalendarDays, parseISO } from 'date-fns';
import { CircleCheck, CircleX, Eye, PencilLine } from 'lucide-react';

export type PayableRow = {
    id: number;
    description: string;
    category: string;
    amount: number | string;
    due_on: string;
    outstanding_cents: number | string;
    status: string;
};

function DueDate({ value }: Readonly<{ value: string }>) {
    const { today } = usePage<{ period: { today: string } }>().props.period;
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

const columnHelper = createAppColumnHelper<PayableRow>();

export const columns = columnHelper.columns([
    columnHelper.accessor('description', {
        header: 'Description',
        meta: { label: 'Description', variant: 'text' },
    }),
    columnHelper.accessor('category', {
        header: 'Category',
        meta: { label: 'Category', variant: 'text' },
    }),
    columnHelper.accessor('amount', {
        header: 'Amount',
        meta: { label: 'Amount', variant: 'number' },
        cell: ({ getValue }) => <ErpMoney amount={Number(getValue())} />,
    }),
    columnHelper.accessor('due_on', {
        header: 'Due date',
        meta: { label: 'Due date', variant: 'date' },
        cell: ({ getValue }) => <DueDate value={getValue()} />,
    }),
    columnHelper.accessor('status', {
        header: 'Status',
        meta: {
            label: 'Status',
            variant: 'select',
            options: choices(['pending', 'partially_paid', 'paid', 'overdue', 'cancelled']),
        },
        cell: ({ getValue }) => (
            <Badge variant={getValue() === 'overdue' ? 'destructive' : 'secondary'}>{humanize(getValue())}</Badge>
        ),
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        meta: { label: 'Actions' },
        enableHiding: false,
        size: 76,
        cell: ({ cell, row: { original: row } }) => (
            <cell.ActionsCell
                modal
                actions={[
                    {
                        label: 'View',
                        href: payables.show.url(row.id),
                        permissions: ['financial-account.show'],
                        icon: Eye,
                    },
                    row.status !== 'cancelled' && {
                        label: 'Edit',
                        href: payables.edit.url(row.id),
                        permissions: ['financial-account.edit'],
                        icon: PencilLine,
                    },
                    Number(row.outstanding_cents) > 0 && {
                        label: 'Confirm payment',
                        href: payables.confirmPayment.url(row.id),
                        permissions: ['financial-account.show', 'cash-entry.create'],
                        icon: CircleCheck,
                    },
                    Number(row.outstanding_cents) === Math.round(Number(row.amount) * 100) && {
                        label: 'Cancel',
                        href: payables.cancel.url(row.id),
                        permissions: ['financial-account.cancel'],
                        icon: CircleX,
                        destructive: true,
                    },
                ]}
            />
        ),
    }),
]);
