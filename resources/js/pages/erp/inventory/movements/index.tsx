import DataTable from '@/components/data-table/data-table';
import type { ServerTableData } from '@/components/data-table/data-table';
import { buttonVariants } from '@/components/ui/button';
import { usePermission } from '@/hooks/use-permission';
import { dashboard } from '@/routes';
import movements from '@/routes/erp/inventory/movements';
import { Head, Link } from '@inertiajs/react';

import { columns } from '@/components/erp/inventory/movements/columns';
import type { MovementRow } from '@/components/erp/inventory/movements/columns';

export default function Movements({ table }: Readonly<{ table: ServerTableData<MovementRow> }>) {
    const { can } = usePermission();

    return (
        <section className="flex min-w-0 flex-col gap-5 p-4 sm:p-6">
            <Head title="Stock movements" />
            <header className="flex flex-wrap items-center justify-between gap-3">
                <h1 className="text-2xl font-semibold tracking-tight">Stock movements</h1>
                {can('stock-movement.create') && (
                    <div className="flex flex-wrap gap-2">
                        <Link href={movements.issue()} className={buttonVariants({ variant: 'outline' })}>
                            New stock issue
                        </Link>
                        <Link href={movements.receipt()} className={buttonVariants()}>
                            New stock receipt
                        </Link>
                    </div>
                )}
            </header>
            <DataTable columns={columns} result={table} url={movements.index.url()} label="Stock movements" />
        </section>
    );
}

Movements.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Stock movements', href: movements.index() },
    ],
};
