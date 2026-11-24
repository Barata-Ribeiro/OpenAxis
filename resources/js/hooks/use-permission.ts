import type { Permission } from '@/types/role-permission';
import { usePage } from '@inertiajs/react';

export function usePermission() {
    const permissions = new Set<string>(usePage().props.auth.permissions ?? []);

    return { can: (...names: Permission['name'][]) => names.some((name) => permissions.has(name)) };
}
