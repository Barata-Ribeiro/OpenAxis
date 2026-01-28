import HeadingSmall from '@/components/common/heading-small';
import EditReceivableForm from '@/components/forms/receivable/edit-receivable-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import type { Receivable } from '@/types/erp/receivable';
import { Head } from '@inertiajs/react';

interface EditReceivablePageProps {
    receivable: Receivable;
}

export default function EditReceivablePage({ receivable }: Readonly<EditReceivablePageProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Receivables', href: erp.receivables.index().url },
        { title: `#${receivable.code}`, href: erp.receivables.show(receivable.id).url },
        { title: `Editing Receivable #${receivable.code}`, href: erp.receivables.edit(receivable.id).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Receivable" />

            <PageLayout title="Edit Receivable" description="Edit an existing receivable in the system.">
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent className="grid gap-2">
                            <HeadingSmall
                                title={`Editing Receivable #${receivable.code}`}
                                description="Update the informations below. Some fields may be restricted at the editing stage."
                            />

                            <EditReceivableForm receivable={receivable} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
