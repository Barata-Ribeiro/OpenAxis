import { AppShell } from '@/components/app-shell';
import { AppContent } from '@/components/application/app-content';
import { AppHeader } from '@/components/application/app-header';
import type { AppLayoutProps } from '@/types';

export default function AppHeaderLayout({ children, breadcrumbs }: Readonly<AppLayoutProps>) {
    return (
        <AppShell variant="header">
            <AppHeader breadcrumbs={breadcrumbs} />
            <AppContent variant="header">{children}</AppContent>
        </AppShell>
    );
}
