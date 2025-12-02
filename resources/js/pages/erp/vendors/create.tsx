import NewVendorForm from '@/components/forms/vendor/new-vendor-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import { BreadcrumbItem } from '@/types';
import { User } from '@/types/application/user';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Vendors', href: erp.vendors.index().url },
    { title: 'New Vendor', href: erp.vendors.create().url },
];

export default function CreateVendorPage({ users }: Readonly<{ users: Array<User> }>) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Vendor" />

            <PageLayout
                title="Create Vendor"
                description="Create a new vendor profile in the system, and associate it with an existing user account that has the vendor role."
            >
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <NewVendorForm users={users} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
