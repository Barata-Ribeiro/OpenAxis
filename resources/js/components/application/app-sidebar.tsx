import { NavFooter } from '@/components/helpers/nav/nav-footer';
import { NavMain } from '@/components/helpers/nav/nav-main';
import { NavUser } from '@/components/helpers/nav/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader } from '@/components/ui/sidebar';
import { usePermission } from '@/hooks/use-permission';
import { sections } from '@/lib/erp-settings';
import { dashboard } from '@/routes';
import { index as cashFlow } from '@/routes/erp/cash-flow';
import { index as inventory } from '@/routes/erp/inventory';
import movements from '@/routes/erp/inventory/movements';
import products from '@/routes/erp/inventory/products';
import type { NavItem } from '@/types';
import type { Permission } from '@/types/role-permission';
import { Link } from '@inertiajs/react';
import {
    ArrowDownToLine,
    ArrowUpFromLine,
    BookOpen,
    CircleDollarSign,
    FolderGit2,
    History,
    LayoutGrid,
    Package,
    PackagePlus,
    Settings2,
} from 'lucide-react';
import AppLogoBig from './app-logo-big';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

const erpItems: (NavItem & { permissions: Permission['name'][] })[] = [
    { title: 'Inventory', href: inventory(), icon: Package, permissions: ['product.index'] },
    { title: 'Stock movements', href: movements.index(), icon: History, permissions: ['stock-movement.index'] },
    {
        title: 'Cash flow',
        href: cashFlow(),
        icon: CircleDollarSign,
        permissions: ['cash-entry.index', 'financial-account.index'],
    },
];

const quickLinks: (NavItem & { permissions: Permission['name'][] })[] = [
    {
        title: 'Stock receipt',
        href: movements.receipt(),
        icon: ArrowDownToLine,
        permissions: ['stock-movement.create'],
    },
    { title: 'Stock issue', href: movements.issue(), icon: ArrowUpFromLine, permissions: ['stock-movement.create'] },
    { title: 'New product', href: products.create(), icon: PackagePlus, permissions: ['product.create'] },
];

export function AppSidebar() {
    const { can } = usePermission();
    const settings = Object.values(sections)
        .filter(({ permission }) => can(`${permission}.index`))
        .map(({ title, routes, icon }) => ({ title, href: routes.index(), icon }));

    return (
        <Sidebar collapsible="offcanvas" variant="floating">
            <SidebarHeader>
                <Link
                    href={dashboard()}
                    aria-label="Go to dashboard"
                    title="Go to dashboard"
                    prefetch="hover"
                    viewTransition
                >
                    <AppLogoBig className="my-1 h-9 w-full" style={{ viewTransitionName: 'app-logo' }} />
                </Link>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
                <NavMain label="ERP" items={erpItems.filter(({ permissions }) => can(...permissions))} />
                <NavMain label="Quick actions" items={quickLinks.filter(({ permissions }) => can(...permissions))} />
                <NavMain label="Settings" icon={Settings2} items={settings} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
