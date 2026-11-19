import { usePage } from '@inertiajs/react';

import AppLogoIcon from '@/components/application/app-logo-icon';

export default function AppLogo() {
    const { name } = usePage().props;

    return (
        <>
            <div className="grid aspect-square size-8 items-center justify-center rounded-md">
                <AppLogoIcon aria-hidden />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate font-mono leading-tight font-semibold text-trim-both">{name}</span>
            </div>
        </>
    );
}
