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

// Partner
export enum PartnerTypes {
    CLIENT = 'client',
    SUPPLIER = 'supplier',
    BOTH = 'both',
}

export const PARTNER_TYPE_LABELS: Record<PartnerTypes, string> = {
    [PartnerTypes.CLIENT]: 'Client',
    [PartnerTypes.SUPPLIER]: 'Supplier',
    [PartnerTypes.BOTH]: 'Supplier and Client',
};

export function partnerTypeLabel(type: PartnerTypes): string {
    return PARTNER_TYPE_LABELS[type] ?? type;
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

// Purchase Order Status
export enum PurchaseOrderStatus {
    PENDING = 'pending',
    APPROVED = 'approved',
    RECEIVED = 'received',
    CANCELED = 'canceled',
}

export const PURCHASE_ORDER_STATUS_LABELS: Record<PurchaseOrderStatus, string> = {
    [PurchaseOrderStatus.PENDING]: 'Pending',
    [PurchaseOrderStatus.APPROVED]: 'Approved',
    [PurchaseOrderStatus.RECEIVED]: 'Received',
    [PurchaseOrderStatus.CANCELED]: 'Canceled',
};

export function purchaseOrderStatusLabel(status: PurchaseOrderStatus): string {
    return PURCHASE_ORDER_STATUS_LABELS[status] ?? status;
}

// Inventory Movement Type
export enum InventoryMovementType {
    INBOUND = 'inbound',
    OUTBOUND = 'outbound',
    ADJUSTMENT = 'adjustment',
}

export const INVENTORY_MOVEMENT_TYPE_LABELS: Record<InventoryMovementType, string> = {
    [InventoryMovementType.INBOUND]: 'Inbound',
    [InventoryMovementType.OUTBOUND]: 'Outbound',
    [InventoryMovementType.ADJUSTMENT]: 'Adjustment',
};

export function inventoryMovementTypeLabel(type: InventoryMovementType): string {
    return INVENTORY_MOVEMENT_TYPE_LABELS[type] ?? type;
}
