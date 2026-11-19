import { NavFooter } from '@/components/helpers/nav/nav-footer';
import { NavMain } from '@/components/helpers/nav/nav-main';
import { NavUser } from '@/components/helpers/nav/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader } from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BookOpen, FolderGit2, LayoutGrid } from 'lucide-react';
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

export function AppSidebar() {
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
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
