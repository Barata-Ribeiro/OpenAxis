// Role
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

// Address
export enum AddressTypes {
    BILLING = 'billing',
    SHIPPING = 'shipping',
    BILLING_AND_SHIPPING = 'billing_and_shipping',
    OTHER = 'other',
}

export const ADDRESS_TYPE_LABELS: Record<AddressTypes, string> = {
    [AddressTypes.BILLING]: 'Billing Address',
    [AddressTypes.SHIPPING]: 'Shipping Address',
    [AddressTypes.BILLING_AND_SHIPPING]: 'Billing and Shipping Address',
    [AddressTypes.OTHER]: 'Other Address',
};

export function addressTypeLabel(type: AddressTypes): string {
    return ADDRESS_TYPE_LABELS[type] ?? type;
}
