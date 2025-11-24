import AppLogoSvg from '@/components/application/app-logo-svg';
import { Button } from '@/components/ui/button';
import { dashboard, login, register } from '@/routes';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Activity } from 'react';

export default function Welcome({ canRegister = true }: Readonly<{ canRegister?: boolean }>) {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Welcome">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className="flex min-h-screen flex-col items-center bg-background p-6 lg:justify-center lg:p-8">
                <header className="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
                    <nav className="flex items-center justify-end gap-4">
                        {auth.user ? (
                            <Button variant="outline" asChild>
                                <Link
                                    href={dashboard()}
                                    as="button"
                                    aria-label="Go to Dashboard"
                                    title="Go to Dashboard"
                                >
                                    Dashboard
                                </Link>
                            </Button>
                        ) : (
                            <>
                                <Button variant="outline" asChild>
                                    <Link
                                        href={login()}
                                        as="button"
                                        aria-label="Go to Login page"
                                        title="Go to Login page"
                                    >
                                        Log in
                                    </Link>
                                </Button>

                                <Activity mode={canRegister ? 'visible' : 'hidden'}>
                                    <Button variant="outline" asChild>
                                        <Link
                                            href={register()}
                                            as="button"
                                            aria-label="Go to Register page"
                                            title="Go to Register page"
                                        >
                                            Register
                                        </Link>
                                    </Button>
                                </Activity>
                            </>
                        )}
                    </nav>
                </header>

                <div className="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
                    <main className="flex w-full max-w-[335px] flex-col-reverse lg:max-w-4xl lg:flex-row">
                        <div className="flex-1 rounded-br-lg rounded-bl-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
                            <AppLogoSvg className="mx-auto mb-8 h-8" />
                            <h1 className="mb-1 font-medium">Let's get started!</h1>
                            <p className="mb-2 text-[#706f6c] dark:text-[#A1A09A]">
                                Welcome to our application! We're excited to have you on board. To get started, please
                                log in if you already have an account, or register to create a new one.
                                <br />
                                <br />
                                If you have any questions or need assistance, feel free to reach out to our support
                                team. We're here to help you make the most of your experience.
                            </p>
                        </div>

                        <div className="relative -mb-px aspect-335/376 w-full shrink-0 overflow-hidden rounded-t-lg bg-[url(https://images.unsplash.com/photo-1565530995968-2e619c04a8a1?q=80&w=432&auto=format&fit=crop)] bg-cover bg-no-repeat lg:mb-0 lg:-ml-px lg:aspect-auto lg:w-[438px] lg:rounded-t-none lg:rounded-r-lg">
                            <div className="absolute inset-0 rounded-t-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-t-none lg:rounded-r-lg dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]" />
                        </div>
                    </main>
                </div>
                <div className="hidden h-14.5 lg:block"></div>
            </div>
        </>
    );
}
