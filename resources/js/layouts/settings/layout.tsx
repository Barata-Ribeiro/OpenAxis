import Heading from '@/components/common/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { cn, isSameUrl, resolveUrl } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { addresses, edit, notifications } from '@/routes/profile';
import { index as sessions } from '@/routes/sessions';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import type { NavItem, SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { useMemo, type PropsWithChildren } from 'react';

const baseSidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: edit(),
        icon: null,
    },
    {
        title: 'Addresses',
        href: addresses(),
        icon: null,
    },
    {
        title: 'Password',
        href: editPassword(),
        icon: null,
    },
    {
        title: 'Two-Factor Auth',
        href: show(),
        icon: null,
    },
    {
        title: 'Appearance',
        href: editAppearance(),
        icon: null,
    },
    {
        title: 'Sessions',
        href: sessions(),
        icon: null,
    },
];

export default function SettingsLayout({ children }: Readonly<PropsWithChildren>) {
    const { auth } = usePage<SharedData>().props;

    const sidebarNavItems = useMemo(() => {
        const items = [...baseSidebarNavItems];

        if (auth.notifications) {
            items.splice(0 + 1, 0, {
                title: `Notifications (${auth.notifications.total_count})`,
                href: notifications(),
                icon: null,
            });
        }

        return items;
    }, [auth.notifications]);

    if (typeof globalThis === 'undefined') return null;

    const currentPath = globalThis.window.location.pathname;

    return (
        <div className="px-4 py-6">
            <Heading title="Settings" description="Manage your profile and account settings" />

            <div className="flex flex-col lg:flex-row lg:space-x-12">
                <aside className="w-full max-w-xl lg:w-48">
                    <nav className="flex flex-col space-y-1 space-x-0">
                        {sidebarNavItems.map((item, index) => (
                            <Button
                                key={`${resolveUrl(item.href)}-${index}`}
                                size="sm"
                                variant="ghost"
                                asChild
                                className={cn('w-full justify-start', {
                                    'bg-muted': isSameUrl(currentPath, item.href),
                                })}
                            >
                                <Link href={item.href} prefetch="hover" viewTransition>
                                    {item.icon && <item.icon className="size-4" />}
                                    {item.title}
                                </Link>
                            </Button>
                        ))}
                    </nav>
                </aside>

                <Separator className="my-6 lg:hidden" />

                <div className="flex-1 md:max-w-2xl">
                    <section className="max-w-xl space-y-12">{children}</section>
                </div>
            </div>
        </div>
    );
}
