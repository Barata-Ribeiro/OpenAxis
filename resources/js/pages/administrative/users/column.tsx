import DropdownMenuCopyButton from '@/components/common/dropdown-menu-copy-button';
import RoleBadge from '@/components/common/role-badge';
import ActionConfirmationDialog from '@/components/feedback/action-confirmation-dialog';
import { DataTableColumnHeader } from '@/components/table/data-table-column-header';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
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
import { useInitials } from '@/hooks/use-initials';
import { usePermission } from '@/hooks/use-permission';
import administrative from '@/routes/administrative';
import profile from '@/routes/profile';
import { SharedData } from '@/types';
import { roleLabel, RoleNames } from '@/types/application/enums';
import { UserWithRelations } from '@/types/application/user';
import { Link, usePage } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CalendarIcon, CircleDashed, DeleteIcon, EditIcon, Ellipsis, EyeIcon, XIcon } from 'lucide-react';
import { useState } from 'react';

export const columns: Array<ColumnDef<UserWithRelations>> = [
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
        cell: function Cell({ row }) {
            const getInitials = useInitials();

            return (
                <div className="inline-flex items-center gap-x-2">
                    <Avatar className="size-6 overflow-hidden rounded-full">
                        <AvatarImage
                            src={row.original.avatar.src ?? ''}
                            srcSet={row.original.avatar.srcSet ?? ''}
                            alt={row.original.name}
                            className="object-cover"
                        />
                        <AvatarFallback className="rounded-lg bg-neutral-200 text-xs text-black select-none dark:bg-neutral-700 dark:text-white">
                            {getInitials(row.original.name)}
                        </AvatarFallback>
                    </Avatar>
                    <span className="truncate font-medium">{row.getValue('name')}</span>
                </div>
            );
        },
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
        cell: function Cell({ row }) {
            const rawRoles = row.original.roles;

            if (!rawRoles || rawRoles.length === 0) {
                return <span className="text-muted-foreground">No Roles</span>;
            }

            return (
                <ul className="flex flex-wrap items-center gap-1">
                    {rawRoles.map((role) => (
                        <RoleBadge key={role.name} role={role} />
                    ))}
                </ul>
            );
        },
        meta: {
            label: 'Roles',
            variant: 'multiSelect',
            options: Object.values(RoleNames).map((role) => ({
                label: roleLabel(role),
                value: role,
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
            const { auth } = usePage<SharedData>().props;
            const { can } = usePermission();
            const [open, setOpen] = useState(false);

            const isCurrentUser = auth.user?.id === row.original.id;
            const canEditUser = can('user.edit') && !isCurrentUser;
            const canDeleteUser = can('user.destroy') && !isCurrentUser;
            const isDeleted = row.original.deleted_at !== null;

            const nameToCopy = row.original.name;
            const emailToCopy = row.original.email;
            const editRoute = canEditUser ? administrative.users.edit(row.original.id) : profile.edit();

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
                            <DropdownMenuLabel>Copy Fields</DropdownMenuLabel>
                            <DropdownMenuGroup>
                                <DropdownMenuItem asChild>
                                    <DropdownMenuCopyButton content={nameToCopy}>Copy Name</DropdownMenuCopyButton>
                                </DropdownMenuItem>
                                <DropdownMenuItem asChild>
                                    <DropdownMenuCopyButton content={emailToCopy}>Copy Email</DropdownMenuCopyButton>
                                </DropdownMenuItem>
                            </DropdownMenuGroup>
                            <DropdownMenuSeparator />
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuGroup>
                                <DropdownMenuItem asChild>
                                    <Link
                                        className="block w-full"
                                        href={administrative.users.show(row.original.id)}
                                        as="button"
                                    >
                                        <EyeIcon aria-hidden size={14} /> View
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem disabled={!can('user.edit')} asChild>
                                    <Link className="block w-full" href={editRoute} as="button">
                                        <EditIcon aria-hidden size={14} /> Edit
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem
                                    variant="destructive"
                                    disabled={!canDeleteUser || isDeleted}
                                    onSelect={() => setOpen(true)}
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
                        description={`Are you sure you want to soft delete user "${row.original.name}"? This action can be undone later.`}
                        method="delete"
                        route={administrative.users.destroy(row.original.id)}
                    />
                </>
            );
        },
        size: 40,
        enableHiding: false,
    },
];
