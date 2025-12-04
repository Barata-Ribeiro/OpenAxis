import DropdownMenuCopyButton from '@/components/common/dropdown-menu-copy-button';
import ActionConfirmationDialog from '@/components/feedback/action-confirmation-dialog';
import { DataTableColumnHeader } from '@/components/table/data-table-column-header';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { usePermission } from '@/hooks/use-permission';
import { normalizeString } from '@/lib/utils';
import administrative from '@/routes/administrative';
import { RoleNames } from '@/types/application/enums';
import { Role } from '@/types/application/role-permission';
import { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { DeleteIcon, Ellipsis, InfinityIcon } from 'lucide-react';
import { useState } from 'react';

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
        cell: ({ row }) => normalizeString(row.original.name),
        enableSorting: true,
    },
    {
        accessorKey: 'guard_name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Guard Name" />,
        cell: ({ row }) => normalizeString(row.original?.guard_name ?? 'N/A'),
        enableSorting: true,
    },
    {
        accessorKey: 'users_count',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Users" />,
        cell: ({ row }) => (
            <Badge
                className="h-5 min-w-5 px-2 font-mono tabular-nums"
                variant="outline"
                aria-label={`${row.original.users_count} user(s)`}
                title={`${row.original.users_count} user(s)`}
            >
                {row.original.users_count}
            </Badge>
        ),
        enableSorting: false,
    },
    {
        accessorKey: 'permissions_count',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Permissions" />,
        cell: function Cell({ row }) {
            const count = row.original.permissions_count;
            const isSuperAdmin = row.original.name === RoleNames.SUPER_ADMIN;
            const label = isSuperAdmin ? 'Unlimited permissions' : `${count} permission(s)`;

            return (
                <Badge
                    className="h-5 min-w-5 px-2 font-mono tabular-nums"
                    variant="outline"
                    aria-label={label}
                    title={label}
                >
                    {isSuperAdmin ? <InfinityIcon aria-hidden size={16} /> : count}
                </Badge>
            );
        },
        enableSorting: false,
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
            const { can } = usePermission();
            const [open, setOpen] = useState(false);

            const nameToCopy = normalizeString(row.original.name);
            const guardNameToCopy = normalizeString(row.original.guard_name ?? 'No Guard Name');

            const deleteRoute = administrative.roles.destroy(row.original.id);
            const canDeleteRole =
                can('role.destroy') && Object.values(RoleNames).every((role) => role !== row.original.name);

            return (
                <>
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
                                <DropdownMenuCopyButton content={guardNameToCopy}>
                                    Copy Guard Name
                                </DropdownMenuCopyButton>
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuGroup>
                                <DropdownMenuItem
                                    variant="destructive"
                                    onSelect={() => setOpen(true)}
                                    disabled={!canDeleteRole}
                                >
                                    <DeleteIcon aria-hidden size={14} /> Delete
                                </DropdownMenuItem>
                            </DropdownMenuGroup>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <ActionConfirmationDialog
                        open={open}
                        setOpen={setOpen}
                        title="Confirm Deletion"
                        description={`Are you sure you want to delete the role "${row.original.name}"? This action cannot be undone later.`}
                        method="delete"
                        route={deleteRoute}
                    />
                </>
            );
        },
        size: 40,
        enableHiding: false,
    },
];
