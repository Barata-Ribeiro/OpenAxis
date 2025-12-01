import { DataTableColumnHeader } from '@/components/table/data-table-column-header';
import { VendorWithRelations } from '@/types/erp/vendor';
import { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CalendarIcon, XIcon } from 'lucide-react';

export const columns: Array<ColumnDef<VendorWithRelations>> = [
    {
        accessorKey: 'id',
        header: ({ column }) => <DataTableColumnHeader column={column} title="ID" />,
        enableSorting: true,
        enableHiding: false,
        size: 40,
    },
    {
        id: 'user.first_name',
        accessorKey: 'full_name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Full Name" />,
        cell: ({ row }) => row.original.full_name,
        enableSorting: true,
        enableHiding: false,
    },
    {
        id: 'user.email',
        accessorKey: 'email',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Email" />,
        cell: ({ row }) => (
            <a
                href={`mailto:${row.original.user.email}`}
                aria-label={`Send email to ${row.original.user.email}`}
                className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
            >
                {row.getValue('email')}
            </a>
        ),
        enableSorting: true,
    },
    {
        accessorKey: 'commission_rate',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Commission Rate" />,
        cell: ({ row }) => `${row.original.commission_rate}%`,
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
    {
        accessorKey: 'deleted_at',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Deleted At" />,
        cell: ({ row }) =>
            row.original.deleted_at ? (
                format(row.original.deleted_at, 'PPpp')
            ) : (
                <XIcon size={14} aria-label="Not deleted" className="text-muted-foreground" />
            ),
        enableSorting: true,
    },
];
