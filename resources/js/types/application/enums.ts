export enum RoleNames {
    SUPER_ADMIN = 'super-admin',
    VENDOR = 'vendor',
    BUYER = 'buyer',
    FINANCE = 'finance',
}

const ROLE_LABELS: Record<RoleNames, string> = {
    [RoleNames.SUPER_ADMIN]: 'Super Admins',
    [RoleNames.VENDOR]: 'Vendors',
    [RoleNames.BUYER]: 'Buyers',
    [RoleNames.FINANCE]: 'Finance Team',
};

export function roleLabel(role: RoleNames): string {
    return ROLE_LABELS[role] ?? role;
}
