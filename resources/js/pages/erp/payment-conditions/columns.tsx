import { DataTableColumnHeader } from '@/components/table/data-table-column-header';
import { Badge } from '@/components/ui/badge';
import { normalizeString } from '@/lib/utils';
import { PaymentCondition } from '@/types/erp/payment-condition';
import { ColumnDef } from '@tanstack/react-table';
import { CircleDashed } from 'lucide-react';

export const columns: Array<ColumnDef<Required<PaymentCondition>>> = [
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
        accessorKey: 'name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Name" />,
        enableSorting: true,
    },
    {
        accessorKey: 'days_until_due',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Days Until Due" />,
        cell: ({ row }) => `${row.original.days_until_due} day(s)`,
        enableSorting: true,
    },
    {
        accessorKey: 'installments',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Installments" />,
        cell: ({ row }) => (
            <Badge
                className="h-5 min-w-5 px-2 font-mono tabular-nums"
                variant="outline"
                aria-label={`${row.original.installments} installment(s)`}
                title={`${row.original.installments} installment(s)`}
            >
                {row.original.installments}
            </Badge>
        ),
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
];
