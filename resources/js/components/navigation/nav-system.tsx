import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { usePermission } from '@/hooks/use-permission';
import { resolveUrl } from '@/lib/utils';
import administrative from '@/routes/administrative';
import { NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { CloudAlertIcon, IdCardLanyardIcon, UsersIcon } from 'lucide-react';
import { Activity } from 'react';

export function NavSystem() {
    const page = usePage();
    const { can } = usePermission();

    const adminItems: NavItem[] = [
        {
            title: 'Users',
            href: administrative.users.index().url,
            icon: UsersIcon,
        },
        {
            title: 'Roles',
            href: administrative.roles.index().url,
            icon: IdCardLanyardIcon,
        },
        {
            title: 'Test Broken Link',
            href: '/non-existent-page',
            icon: CloudAlertIcon,
        },
    ];

    return (
        <SidebarGroup className="px-2 py-0">
            <Activity mode={can('user.index') ? 'visible' : 'hidden'}>
                <SidebarGroupLabel>Administrative</SidebarGroupLabel>
                <SidebarMenu>
                    {adminItems.map((item) => (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url.startsWith(resolveUrl(item.href))}
                                tooltip={{ children: item.title }}
                            >
                                <Link href={item.href} prefetch>
                                    {item.icon && <item.icon />}
                                    <span>{item.title}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    ))}
                </SidebarMenu>
            </Activity>
        </SidebarGroup>
    );
}
