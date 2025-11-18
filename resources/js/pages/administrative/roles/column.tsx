import DropdownMenuCopyButton from '@/components/common/dropdown-menu-copy-button';
import { DataTableColumnHeader } from '@/components/table/data-table-column-header';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { normalizeString } from '@/lib/utils';
import { Role } from '@/types/application/role-permission';
import { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { Ellipsis } from 'lucide-react';

export const columns: Array<ColumnDef<Role>> = [
    {
        accessorKey: 'id',
        header: ({ column }) => <DataTableColumnHeader column={column} title="ID" />,
        enableSorting: true,
        enableHiding: false,
        size: 40,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Name" />,
        cell: ({ row }) => (
            <p>
                {normalizeString(row.original.name)} ({row.original.users_count} users)
            </p>
        ),
        enableSorting: true,
    },
    {
        accessorKey: 'guard_name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Guard Name" />,
        cell: ({ row }) => normalizeString(row.original?.guard_name ?? 'N/A'),
        enableSorting: true,
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Created At" />,
        cell: ({ row }) => format(row.original.created_at, 'PPpp'),
        enableSorting: true,
    },
    {
        accessorKey: 'updated_at',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Updated At" />,
        cell: ({ row }) => format(row.original.updated_at, 'PPpp'),
        enableSorting: true,
    },
    {
        id: 'actions',
        cell: function Cell({ row }) {
            const nameToCopy = normalizeString(row.original.name);
            const guardNameToCopy = normalizeString(row.original.guard_name ?? 'No Guard Name');

            return (
                <DropdownMenu modal={false}>
                    <DropdownMenuTrigger asChild>
                        <Button
                            aria-label="Open menu"
                            variant="ghost"
                            className="flex size-8 p-0 data-[state=open]:bg-muted"
                        >
                            <Ellipsis aria-hidden size={16} />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" className="w-40">
                        <DropdownMenuLabel>Actions</DropdownMenuLabel>
                        <DropdownMenuItem asChild>
                            <DropdownMenuCopyButton content={nameToCopy}>Copy Name</DropdownMenuCopyButton>
                        </DropdownMenuItem>
                        <DropdownMenuItem asChild>
                            <DropdownMenuCopyButton content={guardNameToCopy}>Copy Guard Name</DropdownMenuCopyButton>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            );
        },
        size: 40,
        enableHiding: false,
    },
];
