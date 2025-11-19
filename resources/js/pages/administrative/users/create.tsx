import NewUserForm from '@/components/forms/admin/users/new-user-form';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import administrative from '@/routes/administrative';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Users', href: administrative.users.index().url },
    { title: 'New User', href: administrative.users.create().url },
];

export default function CreateUserPage() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create User" />

            <PageLayout
                title="Create User"
                description="Create a new user account in the system. If password is left blank, a random password will be generated and emailed to the user."
            >
                <NewUserForm />
            </PageLayout>
        </AppLayout>
    );
}
