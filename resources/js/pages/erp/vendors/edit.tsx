import EditVendorForm from '@/components/forms/vendor/edit-vendor-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import type { Vendor } from '@/types/erp/vendor';
import { Head } from '@inertiajs/react';

interface EditVendorPageProps {
    vendor: Vendor;
}

export default function EditVendorPage({ vendor }: Readonly<EditVendorPageProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Vendors', href: erp.vendors.index().url },
        { title: `#${vendor.full_name}`, href: erp.vendors.show(vendor.id).url },
        { title: `Editing #${vendor.full_name}`, href: erp.vendors.edit(vendor.id).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Vendor" />

            <PageLayout title="Edit Vendor" description={`Modify the details of the vendor "${vendor.full_name}".`}>
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <EditVendorForm vendor={vendor} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
