import DataTable from '@/components/data-table/data-table';
import type { ServerTableData } from '@/components/data-table/data-table';
import { ArchiveLinks } from '@/components/erp/records/archive-links';
import { buttonVariants } from '@/components/ui/button';
import { usePermission } from '@/hooks/use-permission';
import { dashboard } from '@/routes';
import { index as inventory } from '@/routes/erp/inventory';
import products from '@/routes/erp/inventory/products';
import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';

import { columns } from '@/components/erp/inventory/products/columns';
import type { ProductRow } from '@/components/erp/inventory/products/columns';

export default function ProductsIndex({
    archived,
    table,
}: Readonly<{ archived: boolean; table: ServerTableData<ProductRow> }>) {
    const { can } = usePermission();
    const activeUrl = products.index.url();
    const archivedUrl = products.index.url({ query: { trashed: 'only' } });
    const url = archived ? archivedUrl : activeUrl;

    return (
        <section className="flex min-w-0 flex-col gap-5 p-4 sm:p-6">
            <Head title="Products" />
            <header className="flex flex-wrap items-center justify-between gap-3">
                <h1 className="text-2xl font-semibold tracking-tight">Products</h1>
                {can('product.create') && (
                    <Link href={products.create()} className={buttonVariants()}>
                        <Plus aria-hidden /> New product
                    </Link>
                )}
            </header>
            <ArchiveLinks activeUrl={activeUrl} archivedUrl={archivedUrl} archived={archived} />
            <DataTable key={url} columns={columns} result={table} url={url} label="Products" />
        </section>
    );
}

ProductsIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Inventory', href: inventory() },
        { title: 'Products', href: products.index() },
    ],
};
