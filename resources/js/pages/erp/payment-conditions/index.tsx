import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import { BreadcrumbItem, PaginationMeta } from '@/types';
import { PaymentCondition } from '@/types/erp/payment-condition';
import { Head } from '@inertiajs/react';
import { columns } from './columns';

interface IndexPageProps {
    paymentConditions: PaginationMeta<Array<PaymentCondition>>;
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
