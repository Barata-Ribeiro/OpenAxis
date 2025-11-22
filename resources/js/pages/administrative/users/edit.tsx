import EditUserForm from '@/components/forms/admin/users/edit-user-form';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import administrative from '@/routes/administrative';
import { BreadcrumbItem } from '@/types';
import { UserWithRelations } from '@/types/application/user';
import { Head } from '@inertiajs/react';

interface EditUserProps {
    user: UserWithRelations;
}

export default function EditUser({ user }: Readonly<EditUserProps>) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Users', href: administrative.users.index().url },
        { title: `#${user.name}`, href: administrative.users.edit(user.id).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Editing ${user.name}`} />

            <PageLayout
                title={`Editing ${user.name}`}
                description="You are editing an existing user; be careful with the changes you make."
            >
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent>
                            <EditUserForm user={user} />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
