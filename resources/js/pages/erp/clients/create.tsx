import NewClientForm from '@/components/forms/client/new-client-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Clients', href: erp.clients.index().url },
    { title: 'New Client', href: erp.clients.create().url },
];

export default function CreateClientPage() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Client" />

            <PageLayout
                title="Create Client"
                description="Create a new client account in the system, with its details and address."
            >
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <NewClientForm />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
