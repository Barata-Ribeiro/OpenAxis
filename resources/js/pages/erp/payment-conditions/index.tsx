import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import { BreadcrumbItem, PaginationMeta } from '@/types';
import { Head } from '@inertiajs/react';

interface IndexPageProps {
    paymentConditions: PaginationMeta<Array<unknown>>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Payment Conditions', href: erp.paymentConditions.index().url }];

export default function IndexPage({ paymentConditions }: Readonly<IndexPageProps>) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Payment Conditions" />

            <PageLayout title="Payment Conditions" description="Listing all the payment conditions in the system.">
                {/* TODO: Implement Payment Conditions Index Page */}
                <pre>{JSON.stringify(paymentConditions, null, 2)}</pre>
            </PageLayout>
        </AppLayout>
    );
}
