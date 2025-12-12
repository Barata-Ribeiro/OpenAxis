import EditClientForm from '@/components/forms/client/edit-client-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import type { Client } from '@/types/erp/client';
import { Head } from '@inertiajs/react';

interface EditClientPageProps {
    client: Client;
}

export default function EditClientPage({ client }: Readonly<EditClientPageProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Clients', href: erp.clients.index().url },
        { title: `#${client.name}`, href: erp.clients.show(client.id).url },
        { title: `Editing #${client.name}`, href: erp.clients.edit(client.id).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Client" />

            <PageLayout title="Edit Client" description={`Modify the details of the client "${client.name}".`}>
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <EditClientForm client={client} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
