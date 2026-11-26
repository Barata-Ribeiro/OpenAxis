import { useFlashToast } from '@/hooks/use-flash-toast';
import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import type { BreadcrumbItem } from '@/types';
import { ModalRoot } from '@inertiaui/modal-react';

export default function AppLayout({
    breadcrumbs = [],
    children,
}: Readonly<{
    breadcrumbs?: BreadcrumbItem[];
    children: React.ReactNode;
}>) {
    useFlashToast();

    return (
        <AppLayoutTemplate breadcrumbs={breadcrumbs}>
            {children}
            <ModalRoot />
        </AppLayoutTemplate>
    );
}
