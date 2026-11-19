import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage } from '@/components/ui/breadcrumb';
import { renderInertiaLink } from '@/lib/inertia-link';
import { toUrl } from '@/lib/utils';
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types';

export function Breadcrumbs({ breadcrumbs }: Readonly<{ breadcrumbs: BreadcrumbItemType[] }>) {
    return (
        <>
            {breadcrumbs.length > 0 && (
                <Breadcrumb>
                    <BreadcrumbList>
                        {breadcrumbs.map((item, index) => (
                            <BreadcrumbItem key={`breadcrumb-${index}-${item.title}`}>
                                {index === breadcrumbs.length - 1 ? (
                                    <BreadcrumbPage>{item.title}</BreadcrumbPage>
                                ) : (
                                    <BreadcrumbLink href={toUrl(item.href)} render={renderInertiaLink(item.href)}>
                                        {item.title}
                                    </BreadcrumbLink>
                                )}
                            </BreadcrumbItem>
                        ))}
                    </BreadcrumbList>
                </Breadcrumb>
            )}
        </>
    );
}
