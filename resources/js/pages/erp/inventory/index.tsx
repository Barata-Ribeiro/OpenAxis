import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { getColumns } from '@/pages/erp/inventory/columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { ProductWithRelations } from '@/types/erp/product';
import { Head } from '@inertiajs/react';

interface IndexPageProps {
    inventory: PaginationMeta<ProductWithRelations[]>;
    categories: string[];
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Inventory', href: erp.inventory.index().url }];

export default function IndexPage({ inventory, categories }: Readonly<IndexPageProps>) {
    const { data, ...pagination } = inventory;
    const { can } = usePermission();

    const columns = getColumns(categories);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Inventory" />

            <PageLayout title="Inventory" description="Listing the products and its stock levels in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('supply.create') ? erp.inventory.create() : undefined}
                />
            </PageLayout>
        </AppLayout>
    );
}
