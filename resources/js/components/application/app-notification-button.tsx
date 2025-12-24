import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { SharedData } from '@/types';
import type { Notification } from '@/types/application/notification';
import { usePage } from '@inertiajs/react';
import { useEchoNotification } from '@laravel/echo-react';
import { BellIcon, MailsIcon } from 'lucide-react';
import { Activity } from 'react';
import { toast } from 'sonner';

export default function AppNotificationButton() {
    const { auth } = usePage<SharedData>().props;

    useEchoNotification<Notification>(`App.Models.User.${auth.user.id}`, (notification) => {
        toast.dismiss();

        toast.info(notification.data.message, { duration: 5000 });
        // TODO: Update notification list and unread count in real-time
    });

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button
                    variant="outline"
                    size="icon"
                    className="relative"
                    aria-label="Notifications"
                    title="Notifications"
                >
                    <BellIcon aria-hidden size={16} />
                    <Activity mode={auth.notifications.unread_count > 0 ? 'visible' : 'hidden'}>
                        <span className="absolute top-0 right-0 inline-flex translate-x-1/2 -translate-y-1/2 transform items-center justify-center rounded-full bg-red-600 px-1.5 py-0.5 text-xs leading-none font-bold text-red-100">
                            {auth.notifications.unread_count}
                        </span>
                    </Activity>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent className="w-56" align="end">
                <DropdownMenuLabel>Notifications</DropdownMenuLabel>
                <DropdownMenuGroup>
                    {/* TODO: Iterate and link to the notification detail page once implemented */}

                    <Activity mode={auth.notifications.latest.length === 0 ? 'visible' : 'hidden'}>
                        <DropdownMenuItem>No new notifications</DropdownMenuItem>
                    </Activity>
                </DropdownMenuGroup>
                <DropdownMenuSeparator />
                {/* TODO: Link to the all notifications page once implemented */}
                <DropdownMenuItem>
                    <MailsIcon aria-hidden size={16} />
                    All notifications ({auth.notifications.total_count})
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
