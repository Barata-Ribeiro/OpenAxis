import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { getColumns } from '@/pages/erp/products/columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { ProductWithRelations } from '@/types/erp/product';
import { Head, usePage } from '@inertiajs/react';

interface IndexPageProps {
    products: PaginationMeta<ProductWithRelations[]>;
    categories: string[];
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Products', href: erp.products.index().url }];

export default function IndexPage({ products, categories }: Readonly<IndexPageProps>) {
    const { url } = usePage();
    const { can } = usePermission();

    const { data, ...pagination } = products;

    const columns = getColumns(categories);

    const queryParams = new URLSearchParams(url.split('?')[1] ?? '');

    const exportables = {
        csvRoute: erp.products.generateCsv({ mergeQuery: Object.fromEntries(queryParams.entries()) }),
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Products" />

            <PageLayout title="Products" description="Listing all the products in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('product.create') ? erp.products.create() : undefined}
                    exportables={exportables}
                />
            </PageLayout>
        </AppLayout>
    );
}
