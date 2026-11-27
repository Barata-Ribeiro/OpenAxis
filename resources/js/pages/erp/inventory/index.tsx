import type { ServerTableData } from '@/components/data-table/data-table';
import DataTable from '@/components/data-table/data-table';
import { Metrics } from '@/components/erp/metrics';
import { buttonVariants } from '@/components/ui/button';
import { useErpFormatting } from '@/hooks/erp/use-erp-formatting';
import { usePermission } from '@/hooks/use-permission';
import { dashboard } from '@/routes';
import { index as inventory } from '@/routes/erp/inventory';
import movements from '@/routes/erp/inventory/movements';
import products from '@/routes/erp/inventory/products';
import { Head, Link } from '@inertiajs/react';
import { PackagePlus, Plus } from 'lucide-react';

import type { ProductRow } from '@/components/erp/inventory/products/columns';
import { columns } from '@/components/erp/inventory/products/columns';
import { Activity } from 'react';

type Summary = Record<'products' | 'critical' | 'in_stock' | 'expiring' | 'expired', number>;

export default function Inventory({
    summary,
    table,
}: Readonly<{ summary: Summary; table: ServerTableData<ProductRow> }>) {
    const { quantity } = useErpFormatting();
    const { can } = usePermission();

    return (
        <section className="flex min-w-0 flex-col gap-5 p-4 sm:p-6">
            <Head title="Inventory" />
            <header className="flex flex-wrap items-center justify-between gap-3">
                <h1 className="text-2xl font-semibold tracking-tight">Inventory</h1>
                <div className="flex flex-wrap gap-2">
                    <Link
                        href={products.index({ query: { trashed: 'only' } })}
                        className={buttonVariants({ variant: 'outline' })}
                    >
                        Archived products
                    </Link>
                    <Activity mode={can('product.create') ? 'visible' : 'hidden'}>
                        <Link href={products.create()} className={buttonVariants({ variant: 'outline' })}>
                            <Plus aria-hidden /> New product
                        </Link>
                    </Activity>
                    <Activity mode={can('stock-movement.create') ? 'visible' : 'hidden'}>
                        <Link href={movements.receipt()} className={buttonVariants()}>
                            <PackagePlus aria-hidden /> New stock receipt
                        </Link>
                    </Activity>
                </div>
            </header>
            <Metrics
                items={[
                    { title: 'Total products', value: quantity.format(summary.products) },
                    { title: 'Critical stock', value: quantity.format(summary.critical) },
                    { title: 'Items in stock', value: quantity.format(summary.in_stock) },
                    { title: 'Expiring within 30 days', value: quantity.format(summary.expiring) },
                    { title: 'Expired', value: quantity.format(summary.expired) },
                ]}
            />
            <DataTable columns={columns} result={table} url={inventory.url()} label="Inventory products" />
        </section>
    );
}

Inventory.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Inventory', href: inventory() },
    ],
};
