import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/erp/clients/columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { Client } from '@/types/erp/client';
import { Head, usePage } from '@inertiajs/react';

interface IndexPageProps {
    clients: PaginationMeta<Client[]>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Clients', href: erp.clients.index().url }];

export default function IndexPage({ clients }: Readonly<IndexPageProps>) {
    const { url } = usePage();
    const { can } = usePermission();

    const { data, ...pagination } = clients;

    const queryParams = new URLSearchParams(url.split('?')[1] ?? '');

    const exportables = {
        csvRoute: erp.clients.generateCsv({ mergeQuery: Object.fromEntries(queryParams.entries()) }),
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Clients" />

            <PageLayout title="Clients" description="Listing all the clients in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('client.create') ? erp.clients.create() : undefined}
                    exportables={exportables}
                />
            </PageLayout>
        </AppLayout>
    );
}
