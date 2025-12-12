import DropdownMenuCopyButton from '@/components/common/dropdown-menu-copy-button';
import DataTableColumnHeader from '@/components/table/data-table-column-header';
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
import type { ProductWithRelations } from '@/types/erp/product';
import { Link } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import { ArrowDownUpIcon, BoxesIcon, CircleDashed, EditIcon, Ellipsis, EyeIcon } from 'lucide-react';

export const getColumns = (categories: string[]): ColumnDef<ProductWithRelations>[] => [
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
        meta: {
            label: 'Inventory Status',
            variant: 'select',
            options: ['in_stock', 'below_minimum', 'out_of_stock'].map((status) => ({
                label: normalizeString(status),
                value: status,
            })),
            icon: CircleDashed,
        },
        enableSorting: true,
        enableColumnFilter: true,
    },
    {
        accessorKey: 'minimum_stock',
        header: ({ column }) => <DataTableColumnHeader column={column} title="Minimum Stock" />,
        cell: ({ row }) => (
            <Badge
                className="h-5 min-w-5 px-2 font-mono tabular-nums"
                variant="outline"
                aria-label={`${row.original.minimum_stock} product(s)`}
                title={`${row.original.minimum_stock} product(s)`}
            >
                {row.original.minimum_stock}
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
            label: 'Product Status',
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
                            <DropdownMenuItem disabled={!can('supply.show')} asChild>
                                <Link className="block w-full" href={erp.inventory.show(row.original.slug)} as="button">
                                    <ArrowDownUpIcon aria-hidden size={14} /> View Movements
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem disabled={!can('supply.edit')} asChild>
                                <Link className="block w-full" href={erp.inventory.edit(row.original.slug)} as="button">
                                    <BoxesIcon aria-hidden size={14} /> Adjust Stock
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem disabled={!can('product.show')} asChild>
                                <Link className="block w-full" href={erp.products.show(row.original.slug)} as="button">
                                    <EyeIcon aria-hidden size={14} /> View Product
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem disabled={!can('supply.edit')} asChild>
                                <Link className="block w-full" href={erp.products.edit(row.original.slug)} as="button">
                                    <EditIcon aria-hidden size={14} /> Edit Product
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
