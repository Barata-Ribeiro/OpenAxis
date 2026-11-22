import AppLogoBig from '@/components/application/app-logo-big';
import { Button } from '@/components/ui/button';
import { renderInertiaLink } from '@/lib/inertia-link';
import { dashboard, login, register } from '@/routes';
import { Head, usePage } from '@inertiajs/react';
import { GaugeIcon, SquareArrowRightEnterIcon, UserPenIcon } from 'lucide-react';
import { Activity } from 'react';

interface WelcomeProps {
    canRegister?: boolean;
}

export default function Welcome({ canRegister = true }: Readonly<WelcomeProps>) {
    const { auth } = usePage().props;

    return (
        <>
            <Head title="Welcome" />

            <div className="flex min-h-screen flex-col items-center bg-background p-6 lg:justify-center lg:p-8">
                <header className="mb-6 w-full max-w-welcome text-sm not-has-[nav]:hidden lg:max-w-4xl">
                    <nav className="flex items-center justify-end gap-4">
                        {auth.user ? (
                            <Button variant="outline" render={renderInertiaLink(dashboard())}>
                                <GaugeIcon aria-hidden data-icon="inline-start" />
                                Dashboard
                            </Button>
                        ) : (
                            <>
                                <Button variant="outline" render={renderInertiaLink(login())}>
                                    <SquareArrowRightEnterIcon aria-hidden data-icon="inline-start" />
                                    Log in
                                </Button>

                                <Activity mode={canRegister ? 'visible' : 'hidden'}>
                                    <Button render={renderInertiaLink(register())}>
                                        <UserPenIcon aria-hidden data-icon="inline-start" />
                                        Register
                                    </Button>
                                </Activity>
                            </>
                        )}
                    </nav>
                </header>

                <div className="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
                    <main className="flex w-full max-w-welcome flex-col-reverse lg:max-w-4xl lg:flex-row">
                        <div className="welcome-panel flex-1 rounded-br-lg rounded-bl-lg p-6 pb-12 leading-5 lg:rounded-tl-lg lg:rounded-br-none lg:p-20">
                            <AppLogoBig className="mx-auto mb-8 h-8" style={{ viewTransitionName: 'app-logo' }} />
                            <h1 className="mb-1 font-medium">Let's get started!</h1>
                            <p className="welcome-description mb-2">
                                Welcome to our application! We're excited to have you on board. To get started, please
                                log in if you already have an account, or register to create a new one.
                                <br />
                                <br />
                                If you have any questions or need assistance, feel free to reach out to our support
                                team. We're here to help you make the most of your experience.
                            </p>
                        </div>

                        <div className="welcome-image relative -mb-px aspect-335/376 w-full shrink-0 overflow-hidden rounded-t-lg bg-cover bg-no-repeat lg:mb-0 lg:-ml-px lg:aspect-auto lg:w-109.5 lg:rounded-t-none lg:rounded-r-lg">
                            <div className="welcome-image-outline absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg" />
                        </div>
                    </main>
                </div>
            </div>
        </>
    );
}
