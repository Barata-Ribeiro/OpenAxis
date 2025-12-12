import { DataTable } from '@/components/table/data-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/erp/inventory/movements-columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { Product, StockMovement } from '@/types/erp/product';
import { Head, Link } from '@inertiajs/react';

interface InventoryShowMovementsProps {
    product: Pick<Product, 'id' | 'name' | 'slug' | 'current_stock' | 'minimum_stock'>;
    movements: PaginationMeta<StockMovement[]>;
}

export default function InventoryShowMovements({ product, movements }: Readonly<InventoryShowMovementsProps>) {
    const { can } = usePermission();
    const { data, ...pagination } = movements;

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Inventory', href: erp.inventory.index().url },
        { title: `#${product.name}`, href: erp.inventory.show(product.slug).url },
    ];

    const currentStock = product.current_stock ?? 0;
    const minimumStock = product.minimum_stock ?? 0;
    const isLowStock = currentStock <= minimumStock;
    const statusLabel = isLowStock ? 'Low' : 'OK';
    const statusVariant = isLowStock ? 'destructive' : 'secondary';

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Inventory: ${product.name}`} />

            <PageLayout
                title={`#${product.name}`}
                description={`Displaying all the inventory movements for "${product.name}".`}
            >
                <Card className="mb-8">
                    <CardHeader className="flex flex-wrap items-center gap-4">
                        <CardTitle className="w-fit">Stock</CardTitle>

                        <Button className="sm:ml-auto" asChild>
                            <Link href={erp.products.show(product.slug)} as="button" prefetch="hover">
                                View Product
                            </Link>
                        </Button>
                    </CardHeader>
                    <CardContent className="flex flex-wrap items-center gap-6">
                        <dl className="flex flex-wrap gap-6">
                            <div>
                                <dt className="text-sm font-medium text-muted-foreground">Current Stock</dt>
                                <dd className="mt-1 text-lg font-medium">{product.current_stock}</dd>
                            </div>

                            <div>
                                <dt className="text-sm font-medium text-muted-foreground">Minimum Stock</dt>
                                <dd className="mt-1 text-lg font-medium">{product.minimum_stock}</dd>
                            </div>
                        </dl>

                        <div className="grid sm:ml-auto">
                            <h3 className="text-sm font-medium text-muted-foreground sm:text-right">Stock Status</h3>

                            <Badge
                                variant={statusVariant}
                                className="select-none sm:justify-self-end"
                                aria-label="Stock status"
                            >
                                {statusLabel}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>

                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('supply.edit') ? erp.inventory.edit(product.slug) : undefined}
                />
            </PageLayout>
        </AppLayout>
    );
}
