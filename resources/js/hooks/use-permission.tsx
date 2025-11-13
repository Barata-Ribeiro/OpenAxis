import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';

export const usePermission = () => {
    const { auth } = usePage<SharedData>().props;
    const permissions = new Set<string>((auth.user?.permissions ?? []).map((p) => p.name));

    const can = (permission: string) => permissions.has(permission);

    return { can };
};
