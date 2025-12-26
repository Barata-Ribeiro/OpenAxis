import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem, ScrollMeta } from '@/types';
import type { Partner } from '@/types/erp/partner';
import type { Product } from '@/types/erp/product';
import { Head } from '@inertiajs/react';

interface CreatePurchaseOrderPageProps {
    suppliers: ScrollMeta<Pick<Partner, 'id' | 'name'>>;
    products: ScrollMeta<Pick<Product, 'id' | 'name' | 'selling_price'>>;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Purchases', href: erp.purchaseOrders.index().url },
    { title: 'New Purchase', href: erp.purchaseOrders.create().url },
];

export default function CreatePurchaseOrderPage({ suppliers, products }: Readonly<CreatePurchaseOrderPageProps>) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Purchase" />

            <PageLayout title="Create Purchase" description="Create a new purchase in the system.">
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            {/* TODO: Purchase Order Form - Suppliers and Products loaded from server. */}
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
