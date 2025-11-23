import EditCategoryForm from '@/components/forms/product/edit-category-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import { BreadcrumbItem } from '@/types';
import { ProductCategory } from '@/types/erp/product-category';
import { Head } from '@inertiajs/react';

export default function EditCategoryPage({ category }: Readonly<{ category: ProductCategory }>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Categories', href: erp.categories.index().url },
        { title: `#${category.name}`, href: erp.categories.edit(category.slug).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Category" />

            <PageLayout title="Edit Category" description={`Modify the details of the category "${category.name}".`}>
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <EditCategoryForm category={category} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
