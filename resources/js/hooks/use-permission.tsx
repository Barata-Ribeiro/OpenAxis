import type { SharedData } from '@/types';
import type { Permission } from '@/types/application/role-permission';
import { usePage } from '@inertiajs/react';

export const usePermission = () => {
    const { auth } = usePage<SharedData>().props;

    const permissions = new Set<string>(auth.permissions ?? []);
    const roles = new Set<string>((auth.user?.roles ?? []).map((r) => r.name));
    const isSuperAdmin = roles.has('super-admin');

    /**
     * Determines whether the current user has at least one of the specified permissions or is a super admin.
     *
     * Checks the `isSuperAdmin` flag first and then tests whether any of the provided permission names
     * exist in the `permissions` collection using its `has` method.
     *
     * @param {...Permission['name'][]} perms - One or more permission names to check.
     *   If none are provided, the function returns `true` only if `isSuperAdmin` is `true`.
     *
     * @returns {boolean} `true` if the user is a super admin or has any of the provided permissions;
     *   otherwise `false`.
     *
     * @example
     * // returns true if user is super admin or has at least one of the listed permissions
     * can('post.read', 'post.edit');
     *
     * @remarks
     * - This function relies on `isSuperAdmin: boolean` and `permissions: { has(name: string): boolean }`
     *   being available in the surrounding scope.
     * - The function is side-effect free and synchronous.
     */
    const can = (...perms: Permission['name'][]) => isSuperAdmin || perms.some((perm) => permissions.has(perm));

    return { can };
};
