import { ErpDate, ErpMoney, ErpQuantity } from '@/components/erp/format';
import { Badge } from '@/components/ui/badge';
import { createAppColumnHelper } from '@/hooks/data-table/table';
import { choices, humanize } from '@/lib/erp/format';
import products from '@/routes/erp/inventory/products';
import { Eye, ImageIcon, PencilLine, RotateCcw, Trash } from 'lucide-react';

export type ProductRow = {
    id: number;
    deleted_at: string | null;
    sku: string;
    image: string | null;
    name: string;
    quantity: number;
    unit: string;
    expires_on: string | null;
    days_remaining: number | null;
    status: string;
    minimum_stock: number;
    category: string | null;
    average_cost: number;
    current_value: number;
};

const statuses = ['in_stock', 'out_of_stock', 'critical', 'expiring', 'expired', 'inactive'];
const columnHelper = createAppColumnHelper<ProductRow>();

export const columns = columnHelper.columns([
    columnHelper.accessor('sku', {
        header: 'SKU',
        meta: { label: 'SKU', variant: 'text' },
    }),
    columnHelper.display({
        id: 'image',
        header: 'Image',
        meta: { label: 'Image' },
        cell: ({ row }) =>
            row.original.image ? (
                <img
                    src={row.original.image}
                    alt=""
                    width={36}
                    height={36}
                    loading="lazy"
                    className="size-9 rounded-md object-cover"
                />
            ) : (
                <span className="grid size-9 place-items-center rounded-md border bg-muted text-muted-foreground">
                    <ImageIcon aria-hidden className="size-4" />
                </span>
            ),
    }),
    columnHelper.accessor('name', {
        header: 'Name',
        meta: { label: 'Name', variant: 'text' },
    }),
    columnHelper.accessor('quantity', {
        header: 'Current quantity',
        meta: { label: 'Current quantity', variant: 'number' },
        cell: ({ getValue }) => <ErpQuantity amount={getValue()} />,
    }),
    columnHelper.accessor('unit', {
        header: 'Unit of measure',
        meta: { label: 'Unit of measure', variant: 'text' },
    }),
    columnHelper.accessor('expires_on', {
        header: 'Expiry date',
        meta: { label: 'Expiry date', variant: 'date' },
        cell: ({ getValue }) => <ErpDate value={getValue()} />,
    }),
    columnHelper.accessor('days_remaining', {
        header: 'Days remaining',
        meta: { label: 'Days remaining', variant: 'number' },
        cell: ({ getValue }) => getValue() ?? '—',
    }),
    columnHelper.accessor('status', {
        header: 'Status',
        meta: { label: 'Status', variant: 'select', options: choices(statuses) },
        cell: ({ getValue }) => (
            <Badge variant={['critical', 'expired', 'out_of_stock'].includes(getValue()) ? 'destructive' : 'secondary'}>
                {humanize(getValue())}
            </Badge>
        ),
    }),
    columnHelper.accessor('minimum_stock', {
        header: 'Minimum stock',
        meta: { label: 'Minimum stock', variant: 'number' },
        cell: ({ getValue }) => <ErpQuantity amount={getValue()} />,
    }),
    columnHelper.accessor('category', {
        header: 'Category',
        meta: { label: 'Category', variant: 'text' },
        cell: ({ getValue }) => getValue() || '—',
    }),
    columnHelper.accessor('average_cost', {
        header: 'Average unit cost',
        meta: { label: 'Average unit cost', variant: 'number' },
        cell: ({ getValue }) => <ErpMoney amount={getValue()} />,
    }),
    columnHelper.accessor('current_value', {
        header: 'Current value',
        meta: { label: 'Current value', variant: 'number' },
        cell: ({ getValue }) => <ErpMoney amount={getValue()} />,
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
                    { label: 'View', href: products.show.url(row.id), permissions: ['product.show'], icon: Eye },
                    !row.deleted_at && {
                        label: 'Edit',
                        href: products.edit.url(row.id),
                        permissions: ['product.edit'],
                        icon: PencilLine,
                    },
                    !row.deleted_at &&
                        row.quantity === 0 && {
                            label: 'Archive',
                            href: products.archive.url(row.id),
                            permissions: ['product.destroy'],
                            icon: Trash,
                            destructive: true,
                        },
                    Boolean(row.deleted_at) && {
                        label: 'Restore',
                        href: products.restore.url(row.id),
                        permissions: ['product.destroy'],
                        icon: RotateCcw,
                        method: 'patch',
                    },
                ]}
            />
        ),
    }),
]);
