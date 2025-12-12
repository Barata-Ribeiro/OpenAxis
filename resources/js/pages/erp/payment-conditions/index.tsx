import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/erp/payment-conditions/columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { PaymentCondition } from '@/types/erp/payment-condition';
import { Head } from '@inertiajs/react';

interface IndexPageProps {
    paymentConditions: PaginationMeta<PaymentCondition[]>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Payment Conditions', href: erp.paymentConditions.index().url }];

export default function IndexPage({ paymentConditions }: Readonly<IndexPageProps>) {
    const { data, ...pagination } = paymentConditions;
    const { can } = usePermission();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Payment Conditions" />

            <PageLayout title="Payment Conditions" description="Listing all the payment conditions in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('finance.create') ? erp.paymentConditions.create() : undefined}
                />
            </PageLayout>
        </AppLayout>
    );
}
