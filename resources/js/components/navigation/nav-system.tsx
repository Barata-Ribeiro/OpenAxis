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
import erp from '@/routes/erp';
import { NavGroup, SharedData } from '@/types';
import { RoleNames } from '@/types/application/enums';
import { Link, usePage } from '@inertiajs/react';
import {
    BookUserIcon,
    FactoryIcon,
    FileUserIcon,
    IdCardLanyardIcon,
    MailIcon,
    Package2Icon,
    PercentIcon,
    ShoppingCartIcon,
    TagsIcon,
    TelescopeIcon,
    TruckIcon,
    UsersIcon,
} from 'lucide-react';
import { Fragment } from 'react';

export function NavSystem() {
    const page = usePage<SharedData>();
    const { can } = usePermission();

    const isSuperAdmin = page.props.auth.user.roles?.some((role) => role.name === RoleNames.SUPER_ADMIN) ?? false;

    const erpGroup: NavGroup = {
        title: 'Enterprise Resource Planning',
        items: [
            {
                title: 'Products',
                href: erp.products.index().url,
                icon: Package2Icon,
                canView: can('product.index'),
            },
            {
                title: 'Categories',
                href: erp.categories.index().url,
                icon: TagsIcon,
                canView: can('product.index'),
            },
            {
                title: 'Clients',
                href: erp.clients.index().url,
                icon: BookUserIcon,
                canView: can('client.index'),
            },
            {
                title: 'Sales',
                href: erp.salesOrders.index().url,
                icon: ShoppingCartIcon,
                canView: can('sale.index'),
            },
            {
                title: 'Vendors',
                href: erp.vendors.index().url,
                icon: FileUserIcon,
                canView: can('vendor.index'),
            },
            {
                title: 'Purchases',
                href: erp.purchaseOrders.index().url,
                icon: TruckIcon,
                canView: can('order.index'),
            },
            {
                title: 'Suppliers',
                href: erp.suppliers.index().url,
                icon: FactoryIcon,
                canView: can('supplier.index'),
            },
            {
                title: 'Payment Conditions',
                href: erp.paymentConditions.index().url,
                icon: PercentIcon,
                canView: can('finance.index'),
            },
        ],
    };

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
            {
                title: 'Telescope',
                href: '/telescope',
                icon: TelescopeIcon,
                canView: isSuperAdmin,
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

    const navigationGroups: NavGroup[] = [erpGroup, adminGroup, mailableGroup];

    return (
        <SidebarGroup className="px-2 py-0">
            {navigationGroups.map((group) => {
                if (group.items.every((item) => item.canView === false)) {
                    return null;
                }

                return (
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
                );
            })}
        </SidebarGroup>
    );
}
