import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import { BreadcrumbItem, PaginationMeta } from '@/types';
import type { Client } from '@/types/erp/client';
import { Head } from '@inertiajs/react';
import { columns } from './columns';

interface IndexPageProps {
    clients: PaginationMeta<Array<Client>>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Clients', href: erp.clients.index().url }];

export default function IndexPage({ clients }: Readonly<IndexPageProps>) {
    const { data, ...pagination } = clients;
    const { can } = usePermission();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Clients" />

            <PageLayout title="Clients" description="Listing all the clients in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('client.create') ? erp.clients.create() : undefined}
                />
            </PageLayout>
        </AppLayout>
    );
}
