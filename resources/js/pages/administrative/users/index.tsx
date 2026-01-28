import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/administrative/users/columns';
import administrative from '@/routes/administrative';
import { type BreadcrumbItem, type PaginationMeta } from '@/types';
import { type UserWithRelations } from '@/types/application/user';
import { Head, usePage } from '@inertiajs/react';

interface IndexPageProps {
    users: PaginationMeta<UserWithRelations[]>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Users', href: administrative.users.index().url }];

export default function IndexPage({ users }: Readonly<IndexPageProps>) {
    const { url } = usePage();
    const { can } = usePermission();

    const { data, ...pagination } = users;

    const queryParams = new URLSearchParams(url.split('?')[1] ?? '');

    const exportables = {
        csvRoute: administrative.users.generateCsv({ mergeQuery: Object.fromEntries(queryParams.entries()) }),
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="System Users" />

            <PageLayout
                title="Users"
                description="Listing all the registered users in the system, including the requesting user."
            >
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('user.create') ? administrative.users.create() : undefined}
                    exportables={exportables}
                />
            </PageLayout>
        </AppLayout>
    );
}
