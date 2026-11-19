import { router } from '@inertiajs/react';
import { LogOut, Settings } from 'lucide-react';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { UserInfo } from '@/components/user-info';
import { renderInertiaLink } from '@/lib/inertia-link';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';

type Props = {
    user: User;
};

export function UserMenuContent({ user }: Props) {
    function handleLogout() {
        router.flushAll();
        router.visit(logout());
    }

    return (
        <>
            <DropdownMenuGroup aria-label="Account">
                <DropdownMenuLabel className="mb-1 border-b border-border p-0 font-normal">
                    <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                        <UserInfo user={user} showEmail={true} />
                    </div>
                </DropdownMenuLabel>
                <DropdownMenuItem
                    className="cursor-pointer"
                    href={edit.url()}
                    textValue="Settings"
                    render={renderInertiaLink(edit(), true)}
                >
                    <Settings className="mr-2" />
                    Settings
                </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuGroup aria-label="Session">
                <DropdownMenuItem
                    className="cursor-pointer"
                    textValue="Log out"
                    onAction={handleLogout}
                    data-test="logout-button"
                >
                    <LogOut className="mr-2" />
                    Log out
                </DropdownMenuItem>
            </DropdownMenuGroup>
        </>
    );
}
