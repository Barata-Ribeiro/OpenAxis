import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/erp/sales/columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { SaleOrderWithRelations } from '@/types/erp/sale-order';
import { Head } from '@inertiajs/react';

interface SalesIndexPageProps {
    sales: PaginationMeta<SaleOrderWithRelations[]>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Sales', href: erp.salesOrders.index().url }];

export default function SalesIndexPage({ sales }: Readonly<SalesIndexPageProps>) {
    const { data, ...pagination } = sales;
    const { can } = usePermission();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Sales" />

            <PageLayout title="Sales" description="Listing all the sales in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('sale.create') ? erp.salesOrders.create() : undefined}
                />
            </PageLayout>
        </AppLayout>
    );
}
