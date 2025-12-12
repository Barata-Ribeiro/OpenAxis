import NewCategoryForm from '@/components/forms/product/new-category-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Categories', href: erp.categories.index().url },
    { title: 'New Category', href: erp.categories.create().url },
];

export default function CreateCategoryPage() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Category" />

            <PageLayout title="Create Category" description="Create a new product category in the system.">
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <NewCategoryForm />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
