import { ErpDate, ErpMoney } from '@/components/erp/format';
import { createAppColumnHelper } from '@/hooks/data-table/table';
import cash from '@/routes/erp/cash-flow';
import { Eye } from 'lucide-react';

export type ExpenseRow = {
    id: number;
    occurred_at: string;
    description: string;
    amount: number | string;
    category: string | null;
};

const columnHelper = createAppColumnHelper<ExpenseRow>();

export const columns = columnHelper.columns([
    columnHelper.accessor('occurred_at', {
        header: 'Date',
        meta: { label: 'Date', variant: 'date' },
        cell: ({ getValue }) => <ErpDate value={getValue()} />,
    }),
    columnHelper.accessor('description', {
        header: 'Description',
        meta: { label: 'Description', variant: 'text' },
    }),
    columnHelper.accessor('category', {
        header: 'Category',
        meta: { label: 'Category', variant: 'text' },
        cell: ({ getValue }) => getValue() ?? '—',
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
