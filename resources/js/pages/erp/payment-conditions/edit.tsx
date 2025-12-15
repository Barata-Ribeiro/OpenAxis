import EditPaymentConditionForm from '@/components/forms/payment-condition/edit-payment-condition-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import type { PaymentCondition } from '@/types/erp/payment-condition';
import { Head } from '@inertiajs/react';

interface EditPaymentConditionPageProps {
    paymentCondition: PaymentCondition;
}

export default function EditPaymentConditionPage({ paymentCondition }: Readonly<EditPaymentConditionPageProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Payment Conditions', href: erp.paymentConditions.index().url },
        { title: `Editing #${paymentCondition.name}`, href: erp.paymentConditions.edit(paymentCondition.code).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Payment Condition" />

            <PageLayout
                title="Edit Payment Condition"
                description={`Modify the details of the payment condition "${paymentCondition.name}".`}
            >
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <EditPaymentConditionForm paymentCondition={paymentCondition} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
