import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import { BreadcrumbItem, PaginationMeta } from '@/types';
import { ProductCategory } from '@/types/erp/product-category';
import { Head } from '@inertiajs/react';
import { columns } from './column';

interface IndexPageProps {
    categories: PaginationMeta<Array<Required<ProductCategory>>>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Categories', href: erp.categories.index().url }];

export default function IndexPage({ categories }: Readonly<IndexPageProps>) {
    const { can } = usePermission();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Product Categories" />

            <PageLayout title="Product Categories" description="Listing all the product categories in the system.">
                <DataTable
                    columns={columns}
                    data={categories.data}
                    pagination={categories}
                    createRoute={can('product.create') ? erp.categories.create() : undefined}
                />
            </PageLayout>
        </AppLayout>
    );
}
