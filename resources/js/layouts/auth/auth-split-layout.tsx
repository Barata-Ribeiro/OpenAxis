import AppLogoBig from '@/components/application/app-logo-big';
import { home } from '@/routes';
import type { AuthLayoutProps } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import backgroundImage from '../../../../public/images/auth-background-image.avif';

export default function AuthSplitLayout({ children, title, description }: Readonly<AuthLayoutProps>) {
    const { name } = usePage().props;

    return (
        <div className="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div className="relative hidden h-full bg-muted p-10 text-background lg:grid dark:border-r">
                <div
                    aria-description="background of Man in a black jacket walking on a brown wooden floor - Free photography on Unsplash by @getrodeo"
                    className="absolute inset-0 bg-foreground"
                    style={{
                        backgroundImage: `url(${backgroundImage})`,
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                    }}
                />
                <div aria-hidden className="absolute inset-0 bg-image-overlay" />
                <Link href={home()} className="relative z-20 self-end text-lg font-medium" aria-label="Home">
                    <AppLogoBig
                        className="h-10 fill-current text-background sm:h-12"
                        aria-label={name}
                        style={{ viewTransitionName: 'app-logo' }}
                    />
                </Link>
            </div>

            <div className="w-full lg:p-8">
                <div className="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-87.5">
                    <Link
                        href={home()}
                        className="relative z-20 flex items-center justify-center lg:hidden"
                        aria-label="Home"
                    >
                        <AppLogoBig
                            className="h-10 fill-current text-background sm:h-12"
                            aria-label={name}
                            style={{ viewTransitionName: 'app-logo' }}
                        />
                    </Link>

                    <div className="flex flex-col items-start gap-2 text-left sm:items-center sm:text-center">
                        <h1 className="text-xl font-medium">{title}</h1>
                        <p className="text-sm text-balance text-muted-foreground">{description}</p>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}
