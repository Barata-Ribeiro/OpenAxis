import { UserInfo } from '@/components/helpers/user-info';
import { UserMenuContent } from '@/components/helpers/user-menu-content';
import { DropdownMenu, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { useIsMobile } from '@/hooks/use-mobile';
import { usePage } from '@inertiajs/react';
import { ChevronsUpDown } from 'lucide-react';

export function NavUser() {
    const { auth } = usePage().props;
    const { state } = useSidebar();
    const { isMobile } = useIsMobile();

    if (!auth.user) {
        return null;
    }

    return (
        <SidebarMenu>
            <SidebarMenuItem>
                <DropdownMenuTrigger>
                    <SidebarMenuButton
                        size="lg"
                        className="group text-sidebar-accent-foreground aria-expanded:bg-sidebar-accent"
                        data-test="sidebar-menu-button"
                    >
                        <UserInfo user={auth.user} />
                        <ChevronsUpDown className="ml-auto size-4" />
                    </SidebarMenuButton>
                    <DropdownMenu
                        className="w-(--trigger-width) min-w-56 rounded-lg"
                        placement={!isMobile && state === 'collapsed' ? 'left bottom' : 'bottom end'}
                    >
                        <UserMenuContent user={auth.user} />
                    </DropdownMenu>
                </DropdownMenuTrigger>
            </SidebarMenuItem>
        </SidebarMenu>
    );
}
