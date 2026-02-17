import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/erp/purchases/columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { PurchaseOrderWithRelations } from '@/types/erp/purchase-order';
import { Head, usePage } from '@inertiajs/react';

interface IndexPageProps {
    purchases: PaginationMeta<PurchaseOrderWithRelations[]>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Purchases', href: erp.purchaseOrders.index().url }];

export default function IndexPage({ purchases }: Readonly<IndexPageProps>) {
    const { url } = usePage();
    const { can } = usePermission();

    const { data, ...pagination } = purchases;

    const queryParams = new URLSearchParams(url.split('?')[1] ?? '');

    const exportables = {
        csvRoute: erp.purchaseOrders.generateCsv({ mergeQuery: Object.fromEntries(queryParams.entries()) }),
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Purchases" />

            <PageLayout title="Purchases" description="Listing all the purchases in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('order.create') ? erp.purchaseOrders.create() : undefined}
                    exportables={exportables}
                />
            </PageLayout>
        </AppLayout>
    );
}
