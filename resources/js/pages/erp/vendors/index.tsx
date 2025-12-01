import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import erp from '@/routes/erp';
import { BreadcrumbItem, PaginationMeta } from '@/types';
import { VendorWithRelations } from '@/types/erp/vendor';
import { Head } from '@inertiajs/react';
import { columns } from './column';

interface IndexPageProps {
    vendors: PaginationMeta<Array<VendorWithRelations>>;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Vendors', href: erp.vendors.index().url }];

export default function IndexPage({ vendors }: Readonly<IndexPageProps>) {
    const { data, ...pagination } = vendors;
    const { can } = usePermission();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Vendors" />

            <PageLayout title="Vendors" description="Listing all the vendors in the system.">
                <DataTable
                    columns={columns}
                    data={data}
                    pagination={pagination}
                    createRoute={can('vendor.create') ? erp.vendors.create() : undefined}
                />
            </PageLayout>
        </AppLayout>
    );
}
