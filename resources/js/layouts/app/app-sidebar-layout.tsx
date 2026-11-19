import { AppContent } from '@/components/application/app-content';
import { AppShell } from '@/components/application/app-shell';
import { AppSidebar } from '@/components/application/app-sidebar';
import { AppSidebarHeader } from '@/components/application/app-sidebar-header';
import type { AppLayoutProps } from '@/types';

export default function AppSidebarLayout({ children, breadcrumbs = [] }: Readonly<AppLayoutProps>) {
    return (
        <AppShell variant="sidebar">
            <AppSidebar />
            <AppContent variant="sidebar" className="min-w-0 overflow-x-clip">
                <AppSidebarHeader breadcrumbs={breadcrumbs} />
                {children}
            </AppContent>
        </AppShell>
    );
}
