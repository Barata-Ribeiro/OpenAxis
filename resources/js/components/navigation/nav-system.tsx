import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { usePermission } from '@/hooks/use-permission';
import { resolveUrl } from '@/lib/utils';
import { telescope } from '@/routes';
import administrative from '@/routes/administrative';
import erp from '@/routes/erp';
import type { NavGroup, SharedData } from '@/types';
import { RoleNames } from '@/types/application/enums';
import { Link, usePage } from '@inertiajs/react';
import { ChevronRight, LandmarkIcon, Package2Icon, ShoppingCartIcon, TruckIcon, UserStarIcon } from 'lucide-react';

export function NavSystem() {
    const page = usePage<SharedData>();
    const { can } = usePermission();

    const isSuperAdmin = page.props.auth.user.roles?.some((role) => role.name === RoleNames.SUPER_ADMIN) ?? false;

    const salesGroup: NavGroup = {
        title: 'Sales',
        icon: ShoppingCartIcon,
        items: [
            {
                title: 'Sales',
                href: erp.salesOrders.index().url,
                canView: can('sale.index'),
            },
            {
                title: 'Clients',
                href: erp.clients.index().url,
                canView: can('client.index'),
            },
            {
                title: 'Vendors',
                href: erp.vendors.index().url,
                canView: can('vendor.index'),
            },
        ],
    };

    const purchaseGroup: NavGroup = {
        title: 'Purchases',
        icon: TruckIcon,
        items: [
            {
                title: 'Purchases',
                href: erp.purchaseOrders.index().url,
                canView: can('order.index'),
            },
            {
                title: 'Suppliers',
                href: erp.suppliers.index().url,
                canView: can('supplier.index'),
            },
        ],
    };

    const produtsGroup: NavGroup = {
        title: 'Products',
        icon: Package2Icon,
        items: [
            {
                title: 'Products',
                href: erp.products.index().url,
                canView: can('product.index'),
            },
            {
                title: 'Categories',
                href: erp.categories.index().url,
                canView: can('product.index'),
            },
            {
                title: 'Inventory',
                href: erp.inventory.index().url,
                canView: can('supply.index'),
            },
        ],
    };

    const financeGroup: NavGroup = {
        title: 'Finance',
        icon: LandmarkIcon,
        items: [
            {
                title: 'Payables',
                href: erp.payables.index().url,
                canView: can('finance.index'),
            },
            {
                title: 'Receivables',
                href: erp.receivables.index().url,
                canView: can('finance.index'),
            },
            {
                title: 'Payment Conditions',
                href: erp.paymentConditions.index().url,
                canView: can('finance.index'),
            },
        ],
    };

    const adminGroup: NavGroup = {
        title: 'Administrative',
        icon: UserStarIcon,
        items: [
            {
                title: 'Users',
                href: administrative.users.index().url,
                canView: can('user.index'),
            },
            {
                title: 'Roles',
                href: administrative.roles.index().url,
                canView: can('role.index'),
            },
            {
                title: 'Telescope',
                href: telescope().url,
                canView: isSuperAdmin,
                isExternal: true,
            },
            {
                title: 'New Account Mail',
                href: administrative.mailable.newAccount().url,
                canView: can('user.create'),
                isExternal: true,
            },
        ],
    };

    const navigationGroups: NavGroup[] = [salesGroup, purchaseGroup, produtsGroup, financeGroup, adminGroup];

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarMenu>
                {navigationGroups.map((group) => {
                    const visibleItems = group.items.filter((item) => item.canView !== false);

                    if (visibleItems.length === 0) return null;

                    const isGroupActive = visibleItems.some((item) => {
                        if (item.isExternal) return false;

                        return page.url.startsWith(resolveUrl(item.href));
                    });

                    return (
                        <Collapsible
                            key={group.title}
                            asChild
                            defaultOpen={isGroupActive}
                            className="group/collapsible"
                        >
                            <SidebarMenuItem>
                                <CollapsibleTrigger asChild>
                                    <SidebarMenuButton tooltip={{ children: group.title }}>
                                        {group.icon && <group.icon />}
                                        <span>{group.title}</span>
                                        <ChevronRight
                                            aria-hidden
                                            className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                        />
                                    </SidebarMenuButton>
                                </CollapsibleTrigger>
                                <CollapsibleContent>
                                    <SidebarMenuSub>
                                        {visibleItems.map((item) => {
                                            const isActive = item.isExternal
                                                ? false
                                                : page.url.startsWith(resolveUrl(item.href));

                                            return (
                                                <SidebarMenuSubItem key={item.title}>
                                                    <SidebarMenuSubButton asChild isActive={isActive}>
                                                        {item.isExternal ? (
                                                            <a
                                                                href={resolveUrl(item.href)}
                                                                target="_blank"
                                                                rel="noreferrer"
                                                            >
                                                                {item.icon && <item.icon />}
                                                                <span>{item.title}</span>
                                                            </a>
                                                        ) : (
                                                            <Link href={item.href} prefetch viewTransition>
                                                                {item.icon && <item.icon />}
                                                                <span>{item.title}</span>
                                                            </Link>
                                                        )}
                                                    </SidebarMenuSubButton>
                                                </SidebarMenuSubItem>
                                            );
                                        })}
                                    </SidebarMenuSub>
                                </CollapsibleContent>
                            </SidebarMenuItem>
                        </Collapsible>
                    );
                })}
            </SidebarMenu>
        </SidebarGroup>
    );
}
