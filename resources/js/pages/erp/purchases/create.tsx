import NewPurchaseOrderForm from '@/components/forms/purchase-order/new-purchase-order-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Purchases', href: erp.purchaseOrders.index().url },
    { title: 'New Purchase', href: erp.purchaseOrders.create().url },
];

export default function CreatePurchaseOrderPage() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="New Purchase" />

            <PageLayout
                title="New Purchase"
                description="Fill in the details to register a new purchase order in the system."
            >
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <NewPurchaseOrderForm />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
