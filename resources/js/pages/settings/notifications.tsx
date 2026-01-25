import HeadingSmall from '@/components/common/heading-small';
import { Button } from '@/components/ui/button';
import { Item, ItemActions, ItemContent, ItemDescription, ItemMedia, ItemTitle } from '@/components/ui/item';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import profile from '@/routes/profile';
import type { BreadcrumbItem, ScrollMeta } from '@/types';
import type { Notification } from '@/types/application/notification';
import { Head, InfiniteScroll, Link } from '@inertiajs/react';
import { formatDistanceToNow } from 'date-fns';
import { InboxIcon, Mail, MailOpen, Trash2 } from 'lucide-react';

interface NotificationsPageProps {
    notifications: ScrollMeta<Notification[]>;
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
                <HeadingSmall title="Inbox" description="Check your notifications" />

                <InfiniteScroll data="notifications" buffer={500}>
                    {notifications.data.map((notification) => {
                        const isUnread = notification.read_at === null;
                        const buttonLabel = isUnread ? 'Mark as read' : 'Mark as unread';

                        return (
                            <Item
                                key={notification.id}
                                variant={isUnread ? 'outline' : 'muted'}
                                className={isUnread ? 'opacity-100' : 'opacity-70'}
                            >
                                <ItemMedia variant="icon">
                                    <InboxIcon aria-hidden />
                                </ItemMedia>
                                <ItemContent>
                                    <ItemTitle>
                                        <p>{notification.data.type}</p>
                                        <time
                                            className="text-xs text-muted-foreground"
                                            dateTime={notification.created_at}
                                        >
                                            {formatDistanceToNow(new Date(notification.created_at), {
                                                addSuffix: true,
                                            })}
                                        </time>
                                    </ItemTitle>
                                    <ItemDescription>{notification.data.message}</ItemDescription>
                                </ItemContent>
                                <ItemActions>
                                    <Button size="icon" aria-label={buttonLabel} title={buttonLabel} asChild>
                                        <Link
                                            href={profile.notifications.toggleRead(notification.id)}
                                            method="patch"
                                            preserveScroll
                                            as="button"
                                        >
                                            {isUnread ? (
                                                <MailOpen aria-hidden size={14} />
                                            ) : (
                                                <Mail aria-hidden size={14} />
                                            )}
                                        </Link>
                                    </Button>

                                    <Button
                                        variant="destructive"
                                        size="icon"
                                        className="text-destructive hover:text-destructive"
                                        aria-label="Delete notification"
                                        title="Delete notification"
                                        asChild
                                    >
                                        <Link
                                            href={profile.notifications.destroy(notification.id)}
                                            method="delete"
                                            preserveScroll
                                            as="button"
                                        >
                                            <Trash2 aria-hidden size={14} />
                                        </Link>
                                    </Button>
                                </ItemActions>
                            </Item>
                        );
                    })}
                </InfiniteScroll>
            </SettingsLayout>
        </AppLayout>
    );
}
