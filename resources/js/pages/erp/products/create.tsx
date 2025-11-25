import NewProductForm from '@/components/forms/product/new-product-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Products', href: erp.products.index().url },
    { title: 'New Product', href: erp.products.create().url },
];

export default function CreateProductPage({ categories }: Readonly<{ categories: string[] }>) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Product" />

            <PageLayout title="Create Product" description="Create a new product in the system.">
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <NewProductForm categories={categories} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
