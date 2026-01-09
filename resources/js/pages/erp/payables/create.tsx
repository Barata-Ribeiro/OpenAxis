import NewPayableForm from '@/components/forms/payable/new-payable-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Payables', href: erp.payables.index().url },
    { title: 'New Payable', href: erp.payables.create().url },
];

export default function CreatePayablePage() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Payable" />

            <PageLayout title="Create Payable" description="Create a new payable in the system.">
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <NewPayableForm />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
