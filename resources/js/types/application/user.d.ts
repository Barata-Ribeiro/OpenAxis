import { Address } from './address';
import { Permission, Role } from './role-permission';

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    two_factor_confirmed_at: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface UserWithRelations extends User {
    roles: (Pick<Role, 'name'> & Partial<Pick<Role, 'id'>>)[];
    permissions?: (Pick<Permission, 'name'> & Partial<Pick<Permission, 'title'>>)[];
    addresses?: Address[];
}
