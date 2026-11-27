import { ErpDate, ErpMoney } from '@/components/erp/format';
import { createAppColumnHelper } from '@/hooks/data-table/table';
import { choices, humanize } from '@/lib/erp/format';
import cash from '@/routes/erp/cash-flow';
import { Eye } from 'lucide-react';

export type ReceiptRow = {
    id: number;
    occurred_at: string;
    description: string;
    amount: number | string;
    type: string;
    payment: string;
};

const columnHelper = createAppColumnHelper<ReceiptRow>();

export const columns = columnHelper.columns([
    columnHelper.accessor('occurred_at', {
        header: 'Date and time',
        meta: { label: 'Date and time', variant: 'date' },
        cell: ({ getValue }) => <ErpDate value={getValue()} withTime />,
    }),
    columnHelper.accessor('type', {
        header: 'Type',
        meta: { label: 'Type', variant: 'select', options: choices(['manual', 'settlement']) },
        cell: ({ getValue }) => humanize(getValue()),
    }),
    columnHelper.accessor('description', {
        header: 'Description',
        meta: { label: 'Description', variant: 'text' },
    }),
    columnHelper.accessor('payment', {
        header: 'Payment method',
        meta: { label: 'Payment method', variant: 'text' },
    }),
    columnHelper.accessor('amount', {
        header: 'Amount',
        meta: { label: 'Amount', variant: 'number' },
        cell: ({ getValue }) => <ErpMoney amount={Number(getValue())} />,
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        meta: { label: 'Actions' },
        enableHiding: false,
        size: 76,
        cell: ({ cell, row }) => (
            <cell.ActionsCell
                modal
                actions={[
                    {
                        label: 'View',
                        href: cash.showEntry.url(row.original.id),
                        permissions: ['cash-entry.show'],
                        icon: Eye,
                    },
                ]}
            />
        ),
    }),
]);
