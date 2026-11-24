import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { renderInertiaLink } from '@/lib/inertia-link';
import { toUrl } from '@/lib/utils';
import type { NavItem } from '@/types';

export function NavMain({ items, label = 'Platform' }: Readonly<{ items: NavItem[]; label?: string }>) {
    const { isCurrentUrl } = useCurrentUrl();

    if (items.length === 0) return null;

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel>{label}</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) => (
                    <SidebarMenuItem key={item.title}>
                        <SidebarMenuButton
                            href={toUrl(item.href)}
                            render={renderInertiaLink(item.href, true)}
                            aria-current={isCurrentUrl(item.href) ? 'page' : undefined}
                            isActive={isCurrentUrl(item.href)}
                            tooltip={{ children: item.title }}
                        >
                            {item.icon && <item.icon />}
                            <span>{item.title}</span>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                ))}
            </SidebarMenu>
        </SidebarGroup>
    );
}
