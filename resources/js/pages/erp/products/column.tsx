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
import { formatCurrency, normalizeString } from '@/lib/utils';
import erp from '@/routes/erp';
import { ProductWithRelations } from '@/types/erp/product';
import { Link } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { format } from 'date-fns/format';
import { CalendarIcon, CircleDashed, EditIcon, Ellipsis, EyeIcon } from 'lucide-react';

export const getColumns = (categories: Array<string>): Array<ColumnDef<ProductWithRelations>> => [
    {
        accessorKey: 'sku',
        header: ({ column }) => <DataTableColumnHeader column={column} title="SKU" />,
        enableSorting: true,
        enableHiding: false,
    },
    {
        accessorKey: 'cover_image',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Cover Image" />,
        cell: function Cell({ row }) {
            const cover_image = row.original.cover_image;
            const altText = `Miniature representation of ${row.original.name}`;

            return cover_image.src && cover_image.srcSet ? (
                <img
                    src={cover_image.src}
                    srcSet={cover_image.srcSet}
                    alt={altText}
                    className="aspect-square size-10 rounded-md object-cover"
                    width={40}
                    height={40}
                />
            ) : (
                <div className="flex size-10 items-center justify-center rounded-md bg-muted text-sm font-medium text-muted-foreground select-none">
                    N/A
                </div>
            );
        },
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Name" />,
        enableSorting: true,
    },
    {
        accessorKey: 'category.name',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Category" />,
        meta: {
            label: 'Categories',
            variant: 'multiSelect',
            options: categories.map((c) => ({
                label: normalizeString(c),
                value: c,
            })),
            icon: CircleDashed,
        },
        enableSorting: true,
        enableColumnFilter: true,
    },
    {
        accessorKey: 'cost_price',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Cost Price" />,
        cell: ({ row }) => formatCurrency(row.original.cost_price),
        enableSorting: true,
    },
    {
        accessorKey: 'selling_price',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Selling Price" />,
        cell: ({ row }) => formatCurrency(row.original.selling_price),
        enableSorting: true,
    },
    {
        accessorKey: 'current_stock',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Current Stock" />,
        cell: ({ row }) => (
            <Badge
                className="h-5 min-w-5 px-2 font-mono tabular-nums"
                variant="outline"
                aria-label={`${row.original.current_stock} product(s)`}
                title={`${row.original.current_stock} product(s)`}
            >
                {row.original.current_stock}
            </Badge>
        ),
        enableSorting: true,
    },
    {
        accessorKey: 'comission',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Comission" />,
        cell: ({ row }) => `${row.original.comission}%`,
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
        id: 'actions',
        cell: function Cell({ row }) {
            const { can } = usePermission();

            const skuToCopy = row.original.sku;
            const nameToCopy = row.original.name;

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
                        <DropdownMenuLabel>Copy Fields</DropdownMenuLabel>
                        <DropdownMenuGroup>
                            <DropdownMenuItem asChild>
                                <DropdownMenuCopyButton content={skuToCopy}>Copy SKU</DropdownMenuCopyButton>
                            </DropdownMenuItem>
                            <DropdownMenuItem asChild>
                                <DropdownMenuCopyButton content={nameToCopy}>Copy Name</DropdownMenuCopyButton>
                            </DropdownMenuItem>
                        </DropdownMenuGroup>
                        <DropdownMenuSeparator />
                        <DropdownMenuLabel>Actions</DropdownMenuLabel>
                        <DropdownMenuGroup>
                            <DropdownMenuItem disabled={!can('product.show')} asChild>
                                <Link className="block w-full" href={erp.products.show(row.original.slug)} as="button">
                                    <EyeIcon aria-hidden size={14} /> View
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem disabled={!can('product.edit')} asChild>
                                <Link className="block w-full" href={erp.products.edit(row.original.slug)} as="button">
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
