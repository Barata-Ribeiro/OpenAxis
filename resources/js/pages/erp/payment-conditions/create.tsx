import NewPaymentConditionForm from '@/components/forms/payment-condition/new-payment-condition-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Payment Conditions', href: erp.paymentConditions.index().url },
    { title: 'New Payment Condition', href: erp.paymentConditions.create().url },
];

export default function CreatePaymentConditionPage() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Payment Condition" />

            <PageLayout
                title="Create Payment Condition"
                description="Create a new payment condition in the system, with its details and terms."
            >
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <NewPaymentConditionForm />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
