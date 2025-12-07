import EditSupplierForm from '@/components/forms/supplier/edit-supplier-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import { BreadcrumbItem } from '@/types';
import { PartnerWithRelations } from '@/types/erp/partner';
import { Head } from '@inertiajs/react';

interface EditSupplierPageProps {
    supplier: PartnerWithRelations;
}

export default function EditSupplierPage({ supplier }: Readonly<EditSupplierPageProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Suppliers', href: erp.suppliers.index().url },
        { title: `#${supplier.name}`, href: erp.suppliers.show(supplier.id).url },
        { title: `Editing #${supplier.name}`, href: erp.suppliers.edit(supplier.id).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Supplier" />

            <PageLayout title="Edit Supplier" description={`Modify the details of the supplier "${supplier.name}".`}>
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <EditSupplierForm supplier={supplier} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
