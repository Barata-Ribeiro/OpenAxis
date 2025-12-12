import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

interface SalesIndexPageProps {
    sales: unknown; // TODO: Define proper type for sales prop
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Sales', href: erp.salesOrders.index().url }];

export default function SalesIndexPage({ sales }: Readonly<SalesIndexPageProps>) {
    // TODO: Define proper type for sales prop
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Sales" />

            <PageLayout title="Sales" description="Listing all the sales in the system.">
                {/* TODO: Implement sales listing table */}
                <pre>{JSON.stringify(sales, null, 2)}</pre>
            </PageLayout>
        </AppLayout>
    );
}
