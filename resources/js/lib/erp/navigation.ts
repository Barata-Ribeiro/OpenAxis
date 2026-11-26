import { dashboard } from '@/routes';
import type { Auth, BreadcrumbItem } from '@/types';
import type { Permission } from '@/types/role-permission';

export function recordLayout(parent: BreadcrumbItem, ...permissions: Permission['name'][]) {
    return ({ title, auth }: { title: string; auth: Auth }) => ({
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            ...(permissions.some((permission) => auth.permissions.includes(permission)) ? [parent] : []),
            { title, href: parent.href },
        ],
    });
}
