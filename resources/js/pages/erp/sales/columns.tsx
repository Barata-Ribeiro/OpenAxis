import DataTableColumnHeader from '@/components/table/data-table-column-header';
import { Badge } from '@/components/ui/badge';
import { formatCurrency } from '@/lib/utils';
import { SaleOrderStatus, saleOrderStatusLabel } from '@/types/erp/erp-enums';
import type { SaleOrderWithRelations } from '@/types/erp/sale-order';
import type { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CalendarIcon, CircleDashed } from 'lucide-react';

export const columns: ColumnDef<SaleOrderWithRelations>[] = [
    {
        accessorKey: 'id',
        header: ({ column }) => <DataTableColumnHeader column={column} title="ID" />,
        enableSorting: true,
        enableHiding: false,
        size: 40,
    },
    {
        accessorKey: 'client.name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Client Name" />,
        enableSorting: true,
    },
    {
        accessorKey: 'vendor.name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Vendor" />,
        enableSorting: true,
    },
    {
        accessorKey: 'total_cost',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Total Cost" />,
        cell: ({ row }) => formatCurrency(row.original.total_cost),
        enableSorting: true,
    },
    {
        accessorKey: 'status',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Status" />,
        cell: function Cell({ row }) {
            const status = row.original.status;
            const variant = status === SaleOrderStatus.CANCELED ? 'destructive' : 'outline';
            const label = `Sale order status is '${saleOrderStatusLabel(status)}'`;

            return (
                <Badge
                    className="h-5 min-w-5 px-2 font-mono tabular-nums"
                    variant={variant}
                    aria-label={label}
                    title={label}
                >
                    {saleOrderStatusLabel(status)}
                </Badge>
            );
        },
        meta: {
            label: 'Sale Order Status',
            variant: 'multiSelect',
            options: Object.values(SaleOrderStatus).map((status) => ({
                label: saleOrderStatusLabel(status),
                value: status,
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

    //TODO: Add actions column
];
