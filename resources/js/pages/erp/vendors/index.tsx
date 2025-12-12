import { DataTable } from '@/components/table/data-table';
import { usePermission } from '@/hooks/use-permission';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import { columns } from '@/pages/erp/vendors/columns';
import erp from '@/routes/erp';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { VendorWithRelations } from '@/types/erp/vendor';
import { Head } from '@inertiajs/react';

interface IndexPageProps {
    vendors: PaginationMeta<VendorWithRelations[]>;
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
