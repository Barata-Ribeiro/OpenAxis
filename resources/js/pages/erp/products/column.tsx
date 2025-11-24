import { DataTableColumnHeader } from '@/components/table/data-table-column-header';
import { Badge } from '@/components/ui/badge';
import { formatCurrency, normalizeString } from '@/lib/utils';
import { ProductWithRelations } from '@/types/erp/product';
import { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns/format';
import { CalendarIcon, CircleDashed } from 'lucide-react';

export const getColumns = (categories: Array<string>): Array<ColumnDef<ProductWithRelations>> => [
    {
        accessorKey: 'sku',
        header: ({ column }) => <DataTableColumnHeader column={column} title="SKU" />,
        enableSorting: true,
        enableHiding: false,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Name" />,
        enableSorting: true,
    },
    {
        id: 'category_name',
        accessorKey: 'category.name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Category" />,
        meta: {
            label: 'Categories',
            variant: 'multiSelect',
            options: categories.map((c) => ({
                label: normalizeString(c),
                value: c,
            })),
            icon: CircleDashed,
        },
        enableSorting: true,
        enableColumnFilter: true,
    },
    {
        accessorKey: 'cost_price',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Cost Price" />,
        cell: ({ row }) => formatCurrency(row.original.cost_price),
        enableSorting: true,
    },
    {
        accessorKey: 'selling_price',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Selling Price" />,
        cell: ({ row }) => formatCurrency(row.original.selling_price),
        enableSorting: true,
    },
    {
        accessorKey: 'current_stock',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Current Stock" />,
        cell: ({ row }) => (
            <Badge
                className="h-5 min-w-5 px-2 font-mono tabular-nums"
                variant="outline"
                aria-label={`${row.original.current_stock} product(s)`}
                title={`${row.original.current_stock} product(s)`}
            >
                {row.original.current_stock}
            </Badge>
        ),
        enableSorting: true,
    },
    {
        accessorKey: 'comission',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Comission" />,
        cell: ({ row }) => `${row.original.comission}%`,
        enableSorting: true,
    },
    {
        accessorKey: 'is_active',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Status" />,
        cell: function Cell({ row }) {
            const isActive = row.original.is_active;
            const statusLabel = isActive ? 'Active' : 'Inactive';
            const statusVariant = isActive ? 'secondary' : 'destructive';

            return (
                <Badge variant={statusVariant} className="select-none">
                    {statusLabel}
                </Badge>
            );
        },
        meta: {
            label: 'Active Status',
            variant: 'select',
            options: ['active', 'inactive'].map((status) => ({
                label: normalizeString(status),
                value: status === 'active' ? 'true' : 'false',
            })),
            icon: CircleDashed,
        },
        enableSorting: true,
        enableColumnFilter: true,
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Created At" />,
        cell: ({ row }) => format(row.original.created_at, 'PPpp'),
        meta: {
            label: 'Created At',
            variant: 'dateRange',
            icon: CalendarIcon,
        },
        enableSorting: true,
    },
    {
        accessorKey: 'updated_at',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Updated At" />,
        cell: ({ row }) => format(row.original.updated_at, 'PPpp'),
        enableSorting: true,
    },
];
