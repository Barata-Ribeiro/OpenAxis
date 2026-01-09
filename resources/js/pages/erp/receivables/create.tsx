import NewReceivableForm from '@/components/forms/receivable/new-receivable-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Receivables', href: erp.receivables.index().url },
    { title: 'New Receivable', href: erp.receivables.create().url },
];

export default function CreateReceivablePage() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Receivable" />

            <PageLayout title="Create Receivable" description="Create a new receivable in the system.">
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <NewReceivableForm />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
