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
import { UserWithRelations } from '@/types/application/user';
import { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { Ellipsis, XIcon } from 'lucide-react';

export const columns: Array<ColumnDef<UserWithRelations>> = [
    {
        accessorKey: 'id',
        header: ({ column }) => <DataTableColumnHeader column={column} title="ID" />,
        enableSorting: true,
        enableHiding: false,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Name" />,
        enableSorting: true,
    },
    {
        accessorKey: 'email',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Email" />,
        cell: ({ row }) => (
            <a
                href={`mailto:${row.original.email}`}
                aria-label={`Send email to ${row.original.email}`}
                className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
            >
                {row.getValue('email')}
            </a>
        ),
        enableSorting: true,
    },
    {
        accessorKey: 'roles',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Role" />,
        cell: ({ row }) => {
            const roles = row.original.roles;
            return roles?.map((role) => role.name).join(', ');
        },
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
    {
        id: 'actions',
        cell: function Cell({ row }) {
            const nameToCopy = row.original.name;
            const emailToCopy = row.original.email;

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
                            <DropdownMenuCopyButton content={emailToCopy}>Copy Email</DropdownMenuCopyButton>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            );
        },
        size: 40,
        enableHiding: false,
    },
];
