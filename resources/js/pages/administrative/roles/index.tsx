import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/administrative/roles/columns';
import administrative from '@/routes/administrative';
import { type PaginationMeta } from '@/types';
import { type Role } from '@/types/application/role-permission';
import { Head } from '@inertiajs/react';

interface IndexPageProps {
    roles: PaginationMeta<Role[]>;
}

export default function IndexPage({ roles }: Readonly<IndexPageProps>) {
    const { data, ...pagination } = roles;
    const { can } = usePermission();

    const createRoute = can('role.create') ? administrative.roles.create() : undefined;

    return (
        <AppLayout>
            <Head title="System Roles" />

            <PageLayout title="Roles" description="Listing all the registered roles in the system.">
                <DataTable columns={columns} data={data} pagination={pagination} createRoute={createRoute} />
            </PageLayout>
        </AppLayout>
    );
}
