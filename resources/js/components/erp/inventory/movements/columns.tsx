import { ErpDate, ErpQuantity } from '@/components/erp/format';
import { createAppColumnHelper } from '@/hooks/data-table/table';
import { choices, humanize } from '@/lib/erp/format';
import movements from '@/routes/erp/inventory/movements';
import { Eye } from 'lucide-react';

export type MovementRow = {
    id: number;
    occurred_at: string;
    type: string;
    product: string;
    batch: string;
    location: string;
    quantity: string;
};

const columnHelper = createAppColumnHelper<MovementRow>();

export const columns = columnHelper.columns([
    columnHelper.accessor('occurred_at', {
        header: 'Date and time',
        meta: { label: 'Date and time', variant: 'date' },
        cell: ({ getValue }) => <ErpDate value={getValue()} withTime />,
    }),
    columnHelper.accessor('type', {
        header: 'Type',
        meta: { label: 'Type', variant: 'select', options: choices(['opening', 'receipt', 'issue', 'return']) },
        cell: ({ getValue }) => humanize(getValue()),
    }),
    columnHelper.accessor('product', {
        header: 'Product',
        meta: { label: 'Product', variant: 'text' },
    }),
    columnHelper.accessor('batch', {
        header: 'Batch',
        meta: { label: 'Batch', variant: 'text' },
    }),
    columnHelper.accessor('location', {
        header: 'Location',
        meta: { label: 'Location', variant: 'text' },
    }),
    columnHelper.accessor('quantity', {
        header: 'Quantity',
        meta: { label: 'Quantity', variant: 'number' },
        cell: ({ getValue }) => <ErpQuantity amount={Number(getValue())} />,
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
                        href: movements.show.url(row.original.id),
                        permissions: ['stock-movement.show'],
                        icon: Eye,
                    },
                ]}
            />
        ),
    }),
]);
