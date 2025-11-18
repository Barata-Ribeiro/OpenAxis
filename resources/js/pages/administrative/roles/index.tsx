import { DataTable } from '@/components/table/data-table';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { PaginationMeta } from '@/types';
import { Role } from '@/types/application/role-permission';
import { Head } from '@inertiajs/react';
import { columns } from './column';

interface IndexPageProps {
    roles: PaginationMeta<Role[]>;
}

export default function IndexPage({ roles }: Readonly<IndexPageProps>) {
    const { data, ...pagination } = roles;
    console.log('Roles data:', data); // Debugging line
    return (
        <AppLayout>
            <Head title="System Roles" />

            <PageLayout title="Roles" description="Listing all the registered roles in the system.">
                <DataTable columns={columns} data={data} pagination={pagination} />
            </PageLayout>
        </AppLayout>
    );
}
