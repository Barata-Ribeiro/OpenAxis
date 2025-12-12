import EditInventoryForm from '@/components/forms/inventory/edit-inventory-form';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import type { Product } from '@/types/erp/product';
import { Head } from '@inertiajs/react';

interface EditInventoryPageProps {
    product: Pick<Product, 'id' | 'name' | 'slug' | 'current_stock' | 'minimum_stock'>;
}

export default function EditInventoryPage({ product }: Readonly<EditInventoryPageProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Inventory', href: erp.inventory.index().url },
        { title: `#${product.name}`, href: erp.inventory.show(product.slug).url },
        { title: `Adjusting #${product.name}`, href: erp.inventory.edit(product.slug).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Adjust Inventory" />

            <PageLayout
                title="Adjust Inventory"
                description={`Adjust the inventory details of the product "${product.name}".`}
            >
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardHeader>
                            <CardTitle>Inventory for &ldquo;{product.name}&rdquo;</CardTitle>
                            <CardDescription>
                                Adjust the current stock of this product as needed, selecting the type of adjustment and
                                providing the necessary details.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <EditInventoryForm product={product} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
