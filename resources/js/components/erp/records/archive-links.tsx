import { buttonVariants } from '@/components/ui/button';
import { Link } from '@inertiajs/react';

export function ArchiveLinks({
    activeUrl,
    archivedUrl,
    archived,
}: Readonly<{
    activeUrl: string;
    archivedUrl: string;
    archived: boolean;
}>) {
    return (
        <nav aria-label="Record status" className="flex gap-2">
            <Link
                href={activeUrl}
                className={buttonVariants({ variant: archived ? 'outline' : 'secondary' })}
                aria-current={archived ? undefined : 'page'}
            >
                Current
            </Link>
            <Link
                href={archivedUrl}
                className={buttonVariants({ variant: archived ? 'secondary' : 'outline' })}
                aria-current={archived ? 'page' : undefined}
            >
                Archived
            </Link>
        </nav>
    );
}
