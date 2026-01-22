import HeadingSmall from '@/components/common/heading-small';
import EditSalesOrderForm from '@/components/forms/sales-order/edit-sales-order-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import type { SaleOrderWithRelations } from '@/types/erp/sale-order';
import { Head } from '@inertiajs/react';

interface EditSalesOrderPageProps {
    saleOrder: SaleOrderWithRelations;
}

export default function EditSalesOrderPage({ saleOrder }: Readonly<EditSalesOrderPageProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Sales', href: erp.salesOrders.index().url },
        { title: `Editing #${saleOrder.id}`, href: erp.salesOrders.edit(saleOrder.id).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Sale" />

            <PageLayout title="Edit Sale" description="Modify the details to update the sale order in the system.">
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent className="grid gap-2">
                            <HeadingSmall
                                title={`Editing Sale Order #${saleOrder.order_number}`}
                                description="Update the informations below. No products can be modified after creation."
                            />

                            <EditSalesOrderForm saleOrder={saleOrder} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
