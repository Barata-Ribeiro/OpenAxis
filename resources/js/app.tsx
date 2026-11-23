import { Toaster } from '@/components/ui/sonner';
import { initializeTheme } from '@/hooks/use-appearance';
import AppLayout from '@/layouts/app-layout';
import AuthLayout from '@/layouts/auth-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { createInertiaApp } from '@inertiajs/react';
import { echo } from '@inertiajs/react/echo';
import { ModalStackProvider } from '@inertiaui/modal-react';
import { configureEcho } from '@laravel/echo-react';

configureEcho({ broadcaster: 'reverb' });

const appName = import.meta.env['VITE_APP_NAME'] || 'Laravel';

void createInertiaApp({
    strictMode: true,
    live: echo(),
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'welcome':
            case name === 'error-page':
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
            <ModalStackProvider>
                {app}
                <Toaster position="top-right" closeButton />
            </ModalStackProvider>
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
