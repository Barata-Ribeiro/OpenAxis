import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import type { User } from '@/types/application/user';
import { Activity } from 'react';

export function UserInfo({ user, showEmail = false }: Readonly<{ user: User; showEmail?: boolean }>) {
    const getInitials = useInitials();

    return (
        <>
            <Avatar className="size-8 overflow-hidden rounded-full">
                <AvatarImage
                    src={user.avatar.src ?? undefined}
                    srcSet={user.avatar.srcSet ?? undefined}
                    alt={user.name}
                />
                <AvatarFallback className="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                    {getInitials(user.name)}
                </AvatarFallback>
            </Avatar>
            <div className="grid flex-1 text-left text-sm leading-tight">
                <span className="truncate font-medium">{user.name}</span>

                <Activity mode={showEmail ? 'visible' : 'hidden'}>
                    <span className="truncate text-xs text-muted-foreground">{user.email}</span>
                </Activity>
            </div>
        </>
    );
}
