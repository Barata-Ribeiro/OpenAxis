import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import { BreadcrumbItem, PaginationMeta } from '@/types';
import { ProductCategory } from '@/types/erp/product-category';
import { Head } from '@inertiajs/react';

interface IndexPageProps {
    products: PaginationMeta<Array<Required<unknown>>>;
    categories: Array<Pick<ProductCategory, 'name'>>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Products', href: erp.products.index().url }];

export default function IndexPage({ products, categories }: Readonly<IndexPageProps>) {
    const { can } = usePermission();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Products" />

            <PageLayout title="Products" description="Listing all the products in the system.">
                <pre>{JSON.stringify({ products, categories }, null, 2)}</pre>
            </PageLayout>
        </AppLayout>
    );
}
