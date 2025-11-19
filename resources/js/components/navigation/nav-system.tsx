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
import { NavGroup } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { IdCardLanyardIcon, MailIcon, UsersIcon } from 'lucide-react';
import { Fragment } from 'react';

export function NavSystem() {
    const page = usePage();
    const { can } = usePermission();

    const adminGroup: NavGroup = {
        title: 'Administrative',
        items: [
            {
                title: 'Users',
                href: administrative.users.index().url,
                icon: UsersIcon,
                canView: can('user.index'),
            },
            {
                title: 'Roles',
                href: administrative.roles.index().url,
                icon: IdCardLanyardIcon,
                canView: can('role.index'),
            },
        ],
    };

    const mailableGroup: NavGroup = {
        title: 'Mailables',
        items: [
            {
                title: 'New Account Mail',
                href: administrative.mailable.newAccount().url,
                icon: MailIcon,
                canView: can('user.create'),
            },
        ],
    };

    const navigationGroups: NavGroup[] = [adminGroup, mailableGroup];

    return (
        <SidebarGroup className="px-2 py-0">
            {navigationGroups.map((group) => (
                <Fragment key={group.title}>
                    <SidebarGroupLabel>{group.title}</SidebarGroupLabel>
                    <SidebarMenu>
                        {group.items
                            .filter((item) => item.canView !== false)
                            .map((item) => (
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
                </Fragment>
            ))}
        </SidebarGroup>
    );
}
