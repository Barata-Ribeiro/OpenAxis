import { useInitials } from '@/hooks/use-initials';
import useIsMounted from '@/hooks/use-mounted';
import { normalizeString } from '@/lib/utils';
import type { User } from '@/types/application/user';
import { useEchoPresence } from '@laravel/echo-react';
import { useEffect, useState } from 'react';
import { Avatar, AvatarBadge, AvatarFallback, AvatarGroupCount, AvatarImage } from '../ui/avatar';
import { AvatarGroup, AvatarGroupTooltip } from '../ui/shadcn-io/avatar-group';

type OnlineUser = Pick<User, 'id' | 'name' | 'avatar'> & { roles: string[] };

export default function AppOnlineUsers() {
    const isMounted = useIsMounted();
    const [users, setUsers] = useState<OnlineUser[]>([]);
    const getInitials = useInitials();
    const { channel, leaveChannel } = useEchoPresence('online', [], (event) => console.debug('Presence event:', event));

    useEffect(() => {
        if (!isMounted) return;

        const presenceChannel = channel();

        presenceChannel.here((users: OnlineUser[]) => {
            setUsers([...new Map(users.map((u) => [u.id, u])).values()]);
        });

        presenceChannel.joining((user: OnlineUser) => {
            setUsers((prev) => (prev.some((u) => u.id === user.id) ? prev : [...prev, user]));
        });

        presenceChannel.leaving((leftUser: OnlineUser) => {
            setUsers((prev) => prev.filter((user) => user.id !== leftUser.id));
        });

        return () => {
            presenceChannel.stopListening('here');
            presenceChannel.stopListening('joining');
            presenceChannel.stopListening('leaving');
            leaveChannel();
        };
    }, [channel, isMounted, leaveChannel]);

    const extraUsersCount = Math.max(0, users.length - 3);

    return (
        <div className="fixed top-4 right-4 z-20 border bg-white shadow-lg">
            <h3 className="border-b px-3 py-1.5 text-sm font-medium">Online Users</h3>
            <div className="bg-muted/20 p-1.5">
                <AvatarGroup variant="css" tooltipProps={{ side: 'bottom', sideOffset: 12 }}>
                    {users.slice(0, 3).map((user) => (
                        <Avatar key={user.id}>
                            <AvatarImage
                                src={user.avatar.src ?? undefined}
                                srcSet={user.avatar.srcSet ?? undefined}
                                alt={user.name}
                            />
                            <AvatarFallback>{getInitials(user.name)}</AvatarFallback>
                            <AvatarBadge className="bg-success" />
                            <AvatarGroupTooltip>
                                <p className="font-semibold">{user.name}</p>
                                <p className="text-sm">{normalizeString(user.roles.at(0) ?? '')}</p>
                            </AvatarGroupTooltip>
                        </Avatar>
                    ))}

                    {extraUsersCount > 0 && (
                        <Avatar className="z-10 text-sm font-medium text-muted-foreground">
                            <AvatarGroupCount>+{extraUsersCount}</AvatarGroupCount>
                            <AvatarGroupTooltip>
                                <p>+{extraUsersCount} more users</p>
                            </AvatarGroupTooltip>
                        </Avatar>
                    )}
                </AvatarGroup>
            </div>
        </div>
    );
}
