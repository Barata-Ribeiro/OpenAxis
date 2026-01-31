import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/erp/payables/columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { PayableWithRelations } from '@/types/erp/payable';
import { Head, usePage } from '@inertiajs/react';

interface IndexPageProps {
    payables: PaginationMeta<PayableWithRelations[]>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Payables', href: erp.payables.index().url }];

export default function IndexPage({ payables }: Readonly<IndexPageProps>) {
    const { url } = usePage();
    const { can } = usePermission();

    const { data, ...pagination } = payables;

    const queryParams = new URLSearchParams(url.split('?')[1] ?? '');

    const exportables = {
        csvRoute: erp.payables.generateCsv({ mergeQuery: Object.fromEntries(queryParams.entries()) }),
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Payables" />

            <PageLayout title="Payables" description="Listing all payables in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('finance.create') ? erp.payables.create() : undefined}
                    exportables={exportables}
                />
            </PageLayout>
        </AppLayout>
    );
}
