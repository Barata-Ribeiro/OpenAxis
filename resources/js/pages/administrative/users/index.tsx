import { DataTable } from '@/components/table/data-table';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { PaginationMeta } from '@/types';
import { UserWithRelations } from '@/types/application/user';
import { Head } from '@inertiajs/react';
import { columns } from './column';

interface IndexPageProps {
    users: PaginationMeta<UserWithRelations[]>;
}

export default function IndexPage({ users }: Readonly<IndexPageProps>) {
    const { data, ...pagination } = users;

    return (
        <AppLayout>
            <Head title="System Users" />

            <PageLayout
                title="Users"
                description="Listing all the registered users in the system, including the requesting user."
            >
                <DataTable columns={columns} data={data} pagination={pagination} />
            </PageLayout>
        </AppLayout>
    );
}
