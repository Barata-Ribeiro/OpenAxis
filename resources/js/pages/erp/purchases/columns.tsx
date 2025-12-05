import { DataTableColumnHeader } from '@/components/table/data-table-column-header';
import { formatCurrency } from '@/lib/utils';
import type { PurchaseOrderWithRelations } from '@/types/erp/purchase-order';
import type { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CalendarIcon } from 'lucide-react';

export const columns: Array<ColumnDef<PurchaseOrderWithRelations>> = [
    {
        accessorKey: 'id',
        header: ({ column }) => <DataTableColumnHeader column={column} title="ID" />,
        enableSorting: true,
        enableHiding: false,
        size: 40,
    },
    {
        id: 'partners.name',
        accessorKey: 'supplier.name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Supplier" />,
        enableSorting: true,
    },
    {
        accessorKey: 'total_cost',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Total Cost" />,
        cell: ({ row }) => formatCurrency(row.original.total_cost),
        enableSorting: true,
    },
    {
        accessorKey: 'status', // TODO: use enum mapping to display user-friendly status
        header: ({ column }) => <DataTableColumnHeader column={column} title="Status" />,
        enableSorting: true,
    },
    {
        id: 'user.name',
        accessorKey: 'user.name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Purchaser" />,
        enableSorting: true,
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
