import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import { BreadcrumbItem, PaginationMeta } from '@/types';
import { ProductWithRelations } from '@/types/erp/product';
import { Head } from '@inertiajs/react';
import { getColumns } from './columns';

interface IndexPageProps {
    inventory: PaginationMeta<Array<ProductWithRelations>>;
    categories: string[];
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Inventory', href: erp.inventory.index().url }];

export default function IndexPage({ inventory, categories }: Readonly<IndexPageProps>) {
    const { data, ...pagination } = inventory;
    const { can } = usePermission();

    console.log({ data, pagination });

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
