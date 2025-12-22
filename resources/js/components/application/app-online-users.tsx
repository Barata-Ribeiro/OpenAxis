import { Avatar, AvatarBadge, AvatarFallback, AvatarGroupCount, AvatarImage } from '@/components/ui/avatar';
import { AvatarGroup, AvatarGroupTooltip } from '@/components/ui/shadcn-io/avatar-group';
import { useInitials } from '@/hooks/use-initials';
import { normalizeString } from '@/lib/utils';
import type { User } from '@/types/application/user';
import { useEchoPresence } from '@laravel/echo-react';
import { useEffect, useRef, useState } from 'react';
import { toast } from 'sonner';

type OnlineUser = Pick<User, 'id' | 'name' | 'avatar'> & { roles: string[] };

interface PresenceChannelWithMembers {
    subscription?: {
        members?: {
            members?: Record<string, OnlineUser>;
        };
    };
}

export default function AppOnlineUsers() {
    const getInitials = useInitials();
    const { channel } = useEchoPresence('online');
    const isBoundRef = useRef(false);
    const [users, setUsers] = useState<OnlineUser[]>(() => {
        const presenceChannel = channel() as unknown as PresenceChannelWithMembers;
        const existingMembers = presenceChannel.subscription?.members?.members;

        if (!existingMembers) return [];

        return [...new Map(Object.values(existingMembers).map((u) => [u.id, u])).values()];
    });

    useEffect(() => {
        if (isBoundRef.current) return;

        isBoundRef.current = true;
        const presenceChannel = channel();

        presenceChannel.here((users: OnlineUser[]) => {
            setUsers([...new Map(users.map((u) => [u.id, u])).values()]);
        });

        presenceChannel.joining((user: OnlineUser) => {
            setUsers((prev) => (prev.some((u) => u.id === user.id) ? prev : [...prev, user]));
            toast.success(`${user.name} is online`, { duration: 3000 });
        });

        presenceChannel.leaving((leftUser: OnlineUser) => {
            setUsers((prev) => prev.filter((user) => user.id !== leftUser.id));
            toast.warning(`${leftUser.name} went offline`, { duration: 3000 });
        });

        return () => {
            isBoundRef.current = false;
        };
    }, [channel]);

    const extraUsersCount = Math.max(0, users.length - 3);

    return (
        <div className="rounded-full border bg-background p-1.5 shadow-lg">
            <AvatarGroup variant="css" tooltipProps={{ side: 'bottom', sideOffset: 12 }}>
                {users.slice(0, 3).map((user) => (
                    <Avatar key={user.id}>
                        <AvatarImage
                            src={user.avatar.src ?? undefined}
                            srcSet={user.avatar.srcSet ?? undefined}
                            alt={user.name}
                        />
                        <AvatarFallback>{getInitials(user.name)}</AvatarFallback>
                        <AvatarBadge aria-hidden className="bg-success">
                            <div className="absolute size-3 animate-ping rounded-full bg-success duration-1000" />
                        </AvatarBadge>
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
    );
}
