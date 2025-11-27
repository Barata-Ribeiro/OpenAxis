import { SharedData } from '@/types';
import { Permission } from '@/types/application/role-permission';
import { usePage } from '@inertiajs/react';

export const usePermission = () => {
    const { auth } = usePage<SharedData>().props;
    const permissions = new Set<string>(auth.permissions ?? []);
    const roles = new Set<string>((auth.user?.roles ?? []).map((r) => r.name));
    const isSuperAdmin = roles.has('super-admin');

    const can = (permission: Permission['name']) => isSuperAdmin || permissions.has(permission);

    return { can };
};
