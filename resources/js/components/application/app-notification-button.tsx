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
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';
import profile from '@/routes/profile';
import type { SharedData } from '@/types';
import type { Notification } from '@/types/application/notification';
import { Link, usePage } from '@inertiajs/react';
import { useEchoModel } from '@laravel/echo-react';
import { formatDistanceToNow } from 'date-fns';
import { BellIcon, Mail, MailOpen, MailsIcon, Trash2 } from 'lucide-react';
import { Activity } from 'react';
import { toast } from 'sonner';

export default function AppNotificationButton() {
    const { auth } = usePage<SharedData>().props;

    const { channel } = useEchoModel('App.Models.User', auth.user.id);

    channel().notification((notification: Notification['data']) => {
        toast.info(notification.message, { duration: 5000 });
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
                    {auth.notifications.latest.map((notification) => {
                        const isUnread = notification.read_at === null;

                        const spanClass = cn('h-3 w-2 rounded', isUnread ? 'bg-primary' : 'bg-muted-foreground/40');
                        const tooltipTriggerClass = cn(
                            'max-w-3xs truncate text-sm',
                            isUnread ? 'font-semibold text-foreground' : 'text-muted-foreground',
                        );

                        const buttonLabel = isUnread ? 'Mark as read' : 'Mark as unread';

                        return (
                            <DropdownMenuItem
                                key={notification.id}
                                className="flex items-start gap-2"
                                onSelect={(event) => event.preventDefault()}
                            >
                                <div className="flex min-w-0 flex-1 flex-col gap-1">
                                    <div className="flex items-center gap-2">
                                        <span className={spanClass} aria-hidden />

                                        <Tooltip>
                                            <TooltipTrigger asChild>
                                                <p className={tooltipTriggerClass}>{notification.data.message}</p>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p className="max-w-xs text-sm">{notification.data.message}</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    </div>
                                    <span className="text-xs text-muted-foreground">
                                        {formatDistanceToNow(new Date(notification.created_at), { addSuffix: true })}
                                    </span>
                                </div>

                                <div className="flex items-center gap-1">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        aria-label={buttonLabel}
                                        title={buttonLabel}
                                        asChild
                                    >
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
                                        variant="ghost"
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
                                </div>
                            </DropdownMenuItem>
                        );
                    })}

                    <Activity mode={auth.notifications.latest.length === 0 ? 'visible' : 'hidden'}>
                        <DropdownMenuItem>No new notifications</DropdownMenuItem>
                    </Activity>
                </DropdownMenuGroup>
                <DropdownMenuSeparator />
                <DropdownMenuItem asChild>
                    <Link className="block w-full" href={profile.notifications()} as="button">
                        <MailsIcon aria-hidden size={16} />
                        All notifications ({auth.notifications.total_count})
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
