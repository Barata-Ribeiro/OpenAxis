import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/erp/suppliers/columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { Partner } from '@/types/erp/partner';
import { Head } from '@inertiajs/react';

interface IndexPageProps {
    suppliers: PaginationMeta<Partner[]>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Suppliers', href: erp.suppliers.index().url }];

export default function IndexPage({ suppliers }: Readonly<IndexPageProps>) {
    const { data, ...pagination } = suppliers;
    const { can } = usePermission();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Suppliers" />

            <PageLayout title="Suppliers" description="Listing all the suppliers in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('supplier.create') ? erp.suppliers.create() : undefined}
                />
            </PageLayout>
        </AppLayout>
    );
}
