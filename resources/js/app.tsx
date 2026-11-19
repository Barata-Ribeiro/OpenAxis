import { Toaster } from '@/components/ui/sonner';
import { initializeTheme } from '@/hooks/use-appearance';
import AppLayout from '@/layouts/app-layout';
import AuthLayout from '@/layouts/auth-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { createInertiaApp } from '@inertiajs/react';

const appName = import.meta.env['VITE_APP_NAME'] || 'Laravel';

void createInertiaApp({
    strictMode: true,
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    withApp(app) {
        return (
            <>
                {app}
                <Toaster position="top-right" closeButton />
            </>
        );
    },
    progress: { color: '#4B5563' },
    defaults: {
        prefetch: {
            cacheFor: '1m',
            hoverDelay: 150,
        },
        visitOptions: (_href, _options) => {
            return { viewTransition: true };
        },
    },
});

// This will set light / dark mode on load...
initializeTheme();
