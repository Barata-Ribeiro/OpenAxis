import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { Head } from '@inertiajs/react';

export default function IndexPage({ users }) {
    console.log(users);
    return (
        <AppLayout>
            <Head title="System Users" />

            <PageLayout
                title="Users"
                description="Listing all the registered users in the system, including the requesting user."
            >
                <p>This is the users page.</p>
            </PageLayout>
        </AppLayout>
    );
}
