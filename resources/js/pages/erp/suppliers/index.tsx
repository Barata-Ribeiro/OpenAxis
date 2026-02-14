import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/erp/suppliers/columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { Partner } from '@/types/erp/partner';
import { Head, usePage } from '@inertiajs/react';

interface IndexPageProps {
    suppliers: PaginationMeta<Partner[]>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Suppliers', href: erp.suppliers.index().url }];

export default function IndexPage({ suppliers }: Readonly<IndexPageProps>) {
    const { url } = usePage();
    const { can } = usePermission();

    const { data, ...pagination } = suppliers;

    const queryParams = new URLSearchParams(url.split('?')[1] ?? '');

    const exportables = {
        csvRoute: erp.suppliers.generateCsv({ mergeQuery: Object.fromEntries(queryParams.entries()) }),
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Suppliers" />

            <PageLayout title="Suppliers" description="Listing all the suppliers in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('supplier.create') ? erp.suppliers.create() : undefined}
                    exportables={exportables}
                />
            </PageLayout>
        </AppLayout>
    );
}
