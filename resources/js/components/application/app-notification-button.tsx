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
import { usePage } from '@inertiajs/react';
import { useEchoNotification } from '@laravel/echo-react';
import { BellIcon, MailsIcon } from 'lucide-react';

export default function AppNotificationButton() {
    const { auth } = usePage<SharedData>().props;

    // TODO: Add notification type handling
    useEchoNotification(`App.Models.User.${auth.user.id}`, (notification) => {
        console.log(notification.type);
        // TODO: Handle incoming notification (e.g., update unread count, show toast, etc.)
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
                    {auth.notifications.unread_count > 0 && (
                        <span className="absolute top-0 right-0 inline-flex translate-x-1/2 -translate-y-1/2 transform items-center justify-center rounded-full bg-red-600 px-1.5 py-0.5 text-xs leading-none font-bold text-red-100">
                            {auth.notifications.unread_count}
                        </span>
                    )}
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent className="w-56" align="end">
                <DropdownMenuLabel>Notifications</DropdownMenuLabel>
                <DropdownMenuGroup>
                    <DropdownMenuItem>{/* TODO: Notification item */}</DropdownMenuItem>
                </DropdownMenuGroup>
                <DropdownMenuSeparator />
                <DropdownMenuItem>
                    <MailsIcon aria-hidden size={16} />
                    All notifications ({auth.notifications.total_count})
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
