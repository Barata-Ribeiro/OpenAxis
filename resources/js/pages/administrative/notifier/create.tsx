import NotifierForm from '@/components/forms/admin/notifier/notifier-form';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page/layout';
import administrative from '@/routes/administrative';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Notifier',
        href: administrative.notifier.create().url,
    },
];

export default function NotifierCreatePage() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Notifier" />

            <PageLayout title="Notifier" description="Send notifications to users.">
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardHeader>
                            <CardTitle>Fill in the details to send a notification</CardTitle>
                            <CardDescription>
                                You can notify users by selecting their roles or by specifying a particular email
                                address. You cannot do both at the same time.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <NotifierForm />
                        </CardContent>
                    </Card>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
