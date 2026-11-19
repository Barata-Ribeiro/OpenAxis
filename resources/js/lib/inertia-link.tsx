import { Link } from '@inertiajs/react';
import type { InertiaLinkProps } from '@inertiajs/react';
import type { ComponentProps } from 'react';

type AriaLinkDOMProps = ComponentProps<'a'> | ComponentProps<'span'> | ComponentProps<'div'>;

export function renderInertiaLink(
    href: NonNullable<InertiaLinkProps['href']>,
    prefetch?: InertiaLinkProps['prefetch'],
) {
    return (props: AriaLinkDOMProps) => (
        // React Aria supplies the anchor's DOM handlers and ref through render.
        <Link {...(props as ComponentProps<typeof Link>)} as="a" href={href} prefetch={prefetch} />
    );
}
