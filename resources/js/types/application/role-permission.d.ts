export interface Role {
    id: number;
    name: string;
    guard_name?: string;
    permissions?: Permission[];
    created_at: string;
    updated_at: string;
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
    name: string;
    guard_name?: string;
    created_at: string;
    updated_at: string;
    roles?: RoleSummary[];
}
