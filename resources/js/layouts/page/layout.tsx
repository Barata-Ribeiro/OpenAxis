import Heading from '@/components/common/heading';
import useIsMounted from '@/hooks/use-mounted';
import { type ReactNode, type PropsWithChildren } from 'react';

interface PageLayoutProps extends PropsWithChildren {
    title: string;
    description: string;
    children: ReactNode;
}

export default function PageLayout({ title, description, children }: Readonly<PageLayoutProps>) {
    const isMounted = useIsMounted();
    if (!isMounted) return null;

    return (
        <div className="px-4 py-6">
            <Heading title={title} description={description} />

            <section aria-label={`Section for ${title}`}>{children}</section>
        </div>
    );
}
