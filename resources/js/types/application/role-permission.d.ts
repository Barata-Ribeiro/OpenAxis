import type { RoleNames } from '@/types/application/enums';

type PermissionAction = 'index' | 'show' | 'create' | 'edit' | 'destroy';

export interface Role {
    id: number;
    name: RoleNames;
    guard_name?: string;
    permissions?: Permission[];
    created_at: string;
    updated_at: string;

    users_count?: number;
    permissions_count?: number;
}

export interface RolePivot {
    permission_id: number;
    role_id: number;
}

export interface RoleSummary extends Omit<Role, 'permissions'> {
    pivot?: RolePivot;
}

export interface Permission {
    id: number;
    title: string;
    name: `${string}.${PermissionAction}`;
    guard_name?: string;
    created_at: string;
    updated_at: string;
    roles?: RoleSummary[];
}
