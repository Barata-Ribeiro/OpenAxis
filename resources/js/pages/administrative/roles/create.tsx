import NewRoleForm from '@/components/forms/admin/roles/new-role-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import administrative from '@/routes/administrative';
import type { BreadcrumbItem } from '@/types';
import type { Permission } from '@/types/application/role-permission';
import { Head } from '@inertiajs/react';

interface CreateRolePageProps {
    permissions: Pick<Permission, 'id' | 'title' | 'name'>[];
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Roles', href: administrative.roles.index().url },
    { title: 'New Role', href: administrative.roles.create().url },
];

export default function CreateRolePage({ permissions }: Readonly<CreateRolePageProps>) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Role" />

            <PageLayout
                title="Create Role"
                description="Create a new role in the system. Assign permissions and manage access control."
            >
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <NewRoleForm permissions={permissions} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
