import NewSupplierForm from '@/components/forms/supplier/new-supplier.form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Suppliers', href: erp.suppliers.index().url },
    { title: 'New Supplier', href: erp.suppliers.create().url },
];

export default function CreateSupplierPage() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Supplier" />

            <PageLayout
                title="Create Supplier"
                description="Create a new supplier account in the system, with its details and address."
            >
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <NewSupplierForm />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
