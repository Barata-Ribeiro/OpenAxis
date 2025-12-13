import NewInventoryMovementForm from '@/components/forms/inventory/new-inventory-movement-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Inventory', href: erp.inventory.index().url },
    { title: 'New Stock Movement', href: erp.inventory.create().url },
];

export default function CreateInventory() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="New Stock Movement" />

            <PageLayout title="New Stock Movement" description="Create a new stock movement in the inventory system.">
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <NewInventoryMovementForm />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
