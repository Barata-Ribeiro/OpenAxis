import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';

export default function ErrorPage({ status, previousRoute }: Readonly<{ status: number; previousRoute: string }>) {
    const title = {
        503: 'Service Unavailable',
        500: 'Server Error',
        404: 'Page Not Found',
        403: 'Forbidden',
    }[status];

    const description = {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
    }[status];

    return (
        <div className="bg-[url(https://images.unsplash.com/photo-1564605503978-7650b149b9c0?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D)] bg-cover bg-center bg-no-repeat">
            <div className="flex min-h-screen flex-col items-center justify-center px-8 py-8 sm:py-16 lg:justify-between lg:py-24">
                <p className="bg-linear-to-b from-white from-30% to-transparent bg-clip-text text-[clamp(10rem,16vw,16.625rem)] leading-none font-bold text-transparent">
                    {status}
                </p>
                <div className="text-center max-lg:mt-36">
                    <h3 className="mb-3 text-5xl font-semibold text-white">{title}</h3>
                    <p className="mb-3 max-w-md text-white/70">{description}</p>
                    <Button asChild>
                        <Link href={previousRoute}>Go Back!</Link>
                    </Button>
                </div>
            </div>
        </div>
    );
}
