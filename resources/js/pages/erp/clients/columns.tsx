import DropdownMenuCopyButton from '@/components/common/dropdown-menu-copy-button';
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
import erp from '@/routes/erp';
import type { Client } from '@/types/erp/client';
import { ClientType, clientTypeLabel } from '@/types/erp/erp-enums';
import { Link } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns';
import { CalendarIcon, CircleDashed, EditIcon, Ellipsis, EyeIcon, XIcon } from 'lucide-react';

export const columns: Array<ColumnDef<Client>> = [
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
        accessorKey: 'identification',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Identification" />,
        enableSorting: true,
    },
    {
        accessorKey: 'client_type',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Type" />,
        cell: function Cell({ row }) {
            const rawClientType = row.original.client_type;

            return <Badge variant="secondary">{normalizeString(rawClientType)}</Badge>;
        },
        meta: {
            label: 'Types',
            variant: 'multiSelect',
            options: Object.values(ClientType).map((type) => ({
                label: clientTypeLabel(type),
                value: type,
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
            const { can } = usePermission();
            const nameToCopy = row.original.name;
            const emailToCopy = row.original.email;
            const idToCopy = row.original.identification;

            return (
                <DropdownMenu modal={false}>
                    {' '}
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
                            <DropdownMenuItem asChild>
                                <DropdownMenuCopyButton content={idToCopy}>Copy Identification</DropdownMenuCopyButton>
                            </DropdownMenuItem>
                        </DropdownMenuGroup>
                        <DropdownMenuSeparator />
                        <DropdownMenuLabel>Actions</DropdownMenuLabel>
                        <DropdownMenuGroup>
                            <DropdownMenuItem disabled={!can('client.show')} asChild>
                                <Link className="block w-full" href={erp.clients.show(row.original.id)} as="button">
                                    <EyeIcon aria-hidden size={14} /> View
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem disabled={!can('client.edit')} asChild>
                                <Link className="block w-full" href={erp.clients.edit(row.original.id)} as="button">
                                    <EditIcon aria-hidden size={14} /> Edit
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuGroup>
                    </DropdownMenuContent>
                </DropdownMenu>
            );
        },
        size: 40,
        enableHiding: false,
    },
];
