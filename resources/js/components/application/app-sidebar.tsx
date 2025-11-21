import { NavFooter } from '@/components/navigation/nav-footer';
import { NavMain } from '@/components/navigation/nav-main';
import { NavSystem } from '@/components/navigation/nav-system';
import { NavUser } from '@/components/navigation/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader } from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BugIcon, Folder, LayoutGrid } from 'lucide-react';
import AppLogoSvg from './app-logo-svg';

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
        href: 'https://github.com/Barata-Ribeiro/OpenAxis',
        icon: Folder,
    },
    {
        title: 'Report a Bug',
        href: 'https://github.com/Barata-Ribeiro/OpenAxis/issues',
        icon: BugIcon,
    },
    {
        title: 'Developed by Barata Ribeiro',
        href: 'https://barataribeiro.com/',
        icon: undefined,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="offcanvas" variant="sidebar">
            <SidebarHeader>
                <Link href={dashboard()} aria-label="Go to dashboard" title="Go to dashboard" prefetch>
                    <AppLogoSvg className="my-1 h-9 w-full" />
                </Link>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
                <NavSystem />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
