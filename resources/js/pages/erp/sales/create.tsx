import NewSalesOrderForm from '@/components/forms/sales-order/new-sales-order-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Sales', href: erp.salesOrders.index().url },
    { title: 'New Sale', href: erp.salesOrders.create().url },
];

export default function CreateSalesOrder() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="New Sale" />

            <PageLayout title="New Sale" description="Fill in the details to register a new sales order in the system.">
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <NewSalesOrderForm />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
