import DataTableColumnHeader from '@/components/table/data-table-column-header';
import { Badge } from '@/components/ui/badge';
import { formatCurrency } from '@/lib/utils';
import { ReceivableStatus, receivableStatusLabel } from '@/types/erp/erp-enums';
import type { ReceivableWithRelations } from '@/types/erp/receivable';
import type { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CalendarIcon, CircleDashed } from 'lucide-react';

export const columns: ColumnDef<ReceivableWithRelations>[] = [
    {
        accessorKey: 'id',
        header: ({ column }) => <DataTableColumnHeader column={column} title="ID" />,
        enableSorting: true,
        enableHiding: false,
        size: 40,
    },
    {
        accessorKey: 'code',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Code" />,
        enableSorting: true,
    },
    {
        accessorKey: 'client.name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Client" />,
        enableSorting: true,
    },
    {
        accessorKey: 'amount',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Amount" />,
        cell: ({ row }) => formatCurrency(row.original.amount),
        enableSorting: true,
    },
    {
        accessorKey: 'due_date',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Due Date" />,
        cell: ({ row }) => format(row.original.due_date, 'PPpp'),
        meta: {
            label: 'Due Date',
            variant: 'dateRange',
            icon: CalendarIcon,
        },
        enableSorting: true,
    },
    {
        accessorKey: 'status',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Status" />,
        cell: function Cell({ row }) {
            const rawStatus = row.original.status;

            return <Badge variant="secondary">{receivableStatusLabel(rawStatus)}</Badge>;
        },
        meta: {
            label: 'Status',
            variant: 'multiSelect',
            options: Object.values(ReceivableStatus).map((status) => ({
                label: receivableStatusLabel(status),
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

    // TODO: Add actions column here in the future
];
