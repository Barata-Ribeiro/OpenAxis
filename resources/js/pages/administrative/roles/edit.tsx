import EditRoleForm from '@/components/forms/admin/roles/edit-role-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import administrative from '@/routes/administrative';
import { BreadcrumbItem } from '@/types';
import { Permission, Role } from '@/types/application/role-permission';
import { Head } from '@inertiajs/react';

interface EditRolePageProps {
    role: Role;
    permissions: Array<Pick<Permission, 'id' | 'title' | 'name'>>;
}

export default function EditRolePage({ role, permissions }: Readonly<EditRolePageProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Roles', href: administrative.roles.index().url },
        { title: `Editing #${role.name}`, href: administrative.roles.edit(role.id).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Role" />

            <PageLayout
                title="Edit Role"
                description="Edit the role in the system. Modify permissions and manage access control."
            >
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <EditRoleForm role={role} permissions={permissions} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
