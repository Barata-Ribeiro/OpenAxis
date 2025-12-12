import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/erp/inventory/movements-columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { Product, StockMovement } from '@/types/erp/product';
import { Head } from '@inertiajs/react';

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

    // TODO: Display info about current stock and minimum stock

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Inventory: ${product.name}`} />

            <PageLayout
                title={`#${product.name}`}
                description={`Displaying all the inventory movements for "${product.name}".`}
            >
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
