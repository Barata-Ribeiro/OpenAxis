import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import profile from '@/routes/profile';
import type { BreadcrumbItem, PaginationMeta } from '@/types';
import type { Notification } from '@/types/application/notification';
import { Head } from '@inertiajs/react';

interface NotificationsPageProps {
    notifications: PaginationMeta<Notification>;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Notifications',
        href: profile.notifications().url,
    },
];

export default function NotificationsPage({ notifications }: Readonly<NotificationsPageProps>) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="My Notifications" />

            <SettingsLayout>
                <pre>{JSON.stringify(notifications, null, 2)}</pre>
            </SettingsLayout>
        </AppLayout>
    );
}
