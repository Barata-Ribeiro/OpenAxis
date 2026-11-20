import { Button } from '@/components/ui/button';
import { renderInertiaLink } from '@/lib/inertia-link';
import { home } from '@/routes';
import { HomeIcon } from 'lucide-react';

const httpErrorMessages = {
    403: { title: 'Forbidden', description: 'Sorry, you are forbidden from accessing this page.' },
    404: { title: 'Not Found', description: 'Sorry, the page you are looking for could not be found.' },
    500: { title: 'Server Error', description: 'Whoops, something went wrong on our servers.' },
    503: { title: 'Service Unavailable', description: 'Sorry, we are doing some maintenance. Please check back soon.' },
} as Record<number, { title: string; description: string }>;

export default function ErrorPage({ status }: Readonly<{ status: number }>) {
    const { title, description } = httpErrorMessages[status] || {
        title: 'Error',
        description: 'An unexpected error occurred.',
    };

    return (
        <main className="relative flex min-h-screen items-center justify-center bg-background px-6 py-16 text-foreground">
            <div
                className="dakr:opacity-20 pointer-events-none absolute inset-0 z-0 mask-radial-from-muted mask-radial-to-transparent mask-radial-at-center opacity-10"
                style={{
                    backgroundImage: `
        repeating-linear-gradient(0deg, transparent, transparent 19px, var(--primary) 19px, var(--primary) 20px, transparent 20px, transparent 39px, var(--primary) 39px, var(--primary) 40px),
        repeating-linear-gradient(90deg, transparent, transparent 19px, var(--primary) 19px, var(--primary) 20px, transparent 20px, transparent 39px, var(--primary) 39px, var(--primary) 40px),
        radial-gradient(circle at 20px 20px, var(--primary) 2px, transparent 2px),
        radial-gradient(circle at 40px 40px, var(--primary) 2px, transparent 2px)
      `,
                    backgroundSize: '40px 40px, 40px 40px, 40px 40px, 40px 40px',
                }}
            />

            <section
                aria-labelledby="error-one-title"
                className="z-10 mx-auto flex w-full max-w-lg flex-col items-center text-center"
            >
                <GradientCode code={status} />

                <div className="flex flex-col items-center gap-2">
                    <h1 id="error-one-title" className="text-xl leading-snug font-bold tracking-tight sm:text-2xl">
                        {title}
                    </h1>
                    <p className="mx-auto max-w-xs text-sm leading-relaxed text-muted-foreground sm:max-w-sm sm:text-base">
                        {description}
                    </p>
                </div>

                <Button size="lg" render={renderInertiaLink(home())}>
                    Go Back Home
                    <HomeIcon aria-hidden data-icon="inline-end" />
                </Button>
            </section>
        </main>
    );
}

function GradientCode({ code }: Readonly<{ code: number }>) {
    return (
        <svg viewBox="0 0 800 300" className="w-full max-w-xs select-none sm:max-w-md" aria-hidden>
            <text
                x="50%"
                y="50%"
                textAnchor="middle"
                dominantBaseline="middle"
                className="fill-primary/20 stroke-primary font-black tracking-tighter"
                style={{ fontSize: '20rem' }}
                strokeWidth="2"
                strokeDasharray="40 20"
            >
                {code}
            </text>
        </svg>
    );
}
