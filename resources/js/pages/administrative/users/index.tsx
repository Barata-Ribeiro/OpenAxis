import { DataTable } from '@/components/table/data-table';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import administrative from '@/routes/administrative';
import { BreadcrumbItem, PaginationMeta } from '@/types';
import { UserWithRelations } from '@/types/application/user';
import { Head } from '@inertiajs/react';
import { columns } from './column';

interface IndexPageProps {
    users: PaginationMeta<UserWithRelations[]>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Users', href: administrative.users.index().url }];

export default function IndexPage({ users }: Readonly<IndexPageProps>) {
    const { data, ...pagination } = users;

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
                    createRoute={administrative.users.create()}
                />
            </PageLayout>
        </AppLayout>
    );
}
