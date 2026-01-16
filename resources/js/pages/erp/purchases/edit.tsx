import HeadingSmall from '@/components/common/heading-small';
import EditPurchaseOrderForm from '@/components/forms/purchase-order/edit-purchase-order-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import type { PurchaseOrderWithRelations } from '@/types/erp/purchase-order';
import { Head } from '@inertiajs/react';

interface EditPurchasePageProps {
    purchaseOrder: PurchaseOrderWithRelations;
}

export default function EditPurchasePage({ purchaseOrder }: Readonly<EditPurchasePageProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Purchases', href: erp.purchaseOrders.index().url },
        { title: `Editing #${purchaseOrder.id}`, href: erp.purchaseOrders.edit(purchaseOrder.id).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Purchase" />

            <PageLayout
                title="Edit Purchase"
                description="Modify the details to update the purchase order in the system."
            >
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent className="grid gap-2">
                            <HeadingSmall
                                title={`Editing Purchase Order #${purchaseOrder.order_number}`}
                                description="Update the informations below. No products can be modified after creation."
                            />

                            <EditPurchaseOrderForm purchaseOrder={purchaseOrder} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
