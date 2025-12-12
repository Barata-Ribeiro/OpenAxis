import EditProductForm from '@/components/forms/product/edit-product-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import type { ProductWithRelations } from '@/types/erp/product';
import { Head } from '@inertiajs/react';

interface EditProductPageProps {
    product: ProductWithRelations;
    categories: string[];
}

export default function EditProductPage({ product, categories }: Readonly<EditProductPageProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Products', href: erp.products.index().url },
        { title: `#${product.name}`, href: erp.products.show(product.slug).url },
        { title: `Editing #${product.name}`, href: erp.products.edit(product.slug).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Product" />

            <PageLayout title="Edit Product" description={`Modify the details of the product "${product.name}".`}>
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <EditProductForm product={product} categories={categories} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
