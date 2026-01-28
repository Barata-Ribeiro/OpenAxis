import HeadingSmall from '@/components/common/heading-small';
import EditPayableForm from '@/components/forms/payable/edit-payable-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import type { Payable } from '@/types/erp/payable';
import { Head } from '@inertiajs/react';

interface EditPayablePageProps {
    payable: Payable;
}

export default function EditPayablePage({ payable }: Readonly<EditPayablePageProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Payables',
            href: erp.payables.index().url,
        },
        {
            title: `#${payable.code}`,
            href: erp.payables.show(payable.id).url,
        },
        {
            title: `Editing Payable #${payable.code}`,
            href: erp.payables.edit(payable.id).url,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Purchase" />

            <PageLayout title="Edit Payable" description="Modify the details to update the payable in the system.">
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent className="grid gap-2">
                            <HeadingSmall
                                title={`Editing Payable #${payable.code}`}
                                description="Update the informations below. Some fields may be restricted at the editing stage."
                            />

                            <EditPayableForm payable={payable} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
