import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/erp/receivables/columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { ReceivableWithRelations } from '@/types/erp/receivable';
import { Head } from '@inertiajs/react';

interface IndexPageProps {
    receivables: PaginationMeta<ReceivableWithRelations[]>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Receivables', href: erp.receivables.index().url }];

export default function IndexPage({ receivables }: Readonly<IndexPageProps>) {
    const { data, ...pagination } = receivables;
    const { can } = usePermission();
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Receivables" />

            <PageLayout title="Receivables" description="Listing all receivables in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('finance.create') ? erp.receivables.create() : undefined}
                />
            </PageLayout>
        </AppLayout>
    );
}
