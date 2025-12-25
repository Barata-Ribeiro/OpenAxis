import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/erp/payables/columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import { Head } from '@inertiajs/react';

interface IndexPageProps {
    payables: PaginationMeta<unknown[]>; // TODO: Replace 'unknown' with the actual Payable type when available
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Payables', href: erp.payables.index().url }];

export default function IndexPage({ payables }: Readonly<IndexPageProps>) {
    const { data, ...pagination } = payables;
    const { can } = usePermission();
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Payables" />

            <PageLayout title="Payables" description="Listing all payables in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('finance.create') ? erp.payables.create() : undefined}
                />
            </PageLayout>
        </AppLayout>
    );
}
