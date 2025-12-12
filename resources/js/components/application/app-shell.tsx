import { SidebarProvider } from '@/components/ui/sidebar';
import { Toaster } from '@/components/ui/sonner';
import useIsMounted from '@/hooks/use-mounted';
import type { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';

interface AppShellProps {
    children: React.ReactNode;
    variant?: 'header' | 'sidebar';
}

export function AppShell({ children, variant = 'header' }: Readonly<AppShellProps>) {
    const { sidebarOpen: isOpen, flash } = usePage<SharedData>().props;
    const isMounted = useIsMounted();

    useEffect(() => {
        if (!isMounted) return;

        if (!flash || !Object.values(flash).some(Boolean)) return;

        if (flash.success) toast.success(flash.success, { id: 'flash-success' });
        else if (flash.error) toast.error(flash.error, { id: 'flash-error' });
        else if (flash.info) toast(flash.info, { id: 'flash-info' });
        else if (flash.warning) toast.warning(flash.warning, { id: 'flash-warning' });

        return () => {
            toast.dismiss();
        };
    }, [flash, isMounted]);

    if (variant === 'header') {
        return <div className="flex min-h-screen w-full flex-col">{children}</div>;
    }

    return (
        <SidebarProvider defaultOpen={isOpen}>
            {children}
            <Toaster />
        </SidebarProvider>
    );
}
