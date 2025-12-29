// Client Type
export enum ClientType {
    INDIVIDUAL = 'individual',
    COMPANY = 'company',
}

export const CLIENT_TYPE_LABELS: Record<ClientType, string> = {
    [ClientType.INDIVIDUAL]: 'Individual',
    [ClientType.COMPANY]: 'Company',
};

export function clientTypeLabel(type: ClientType): string {
    return CLIENT_TYPE_LABELS[type] ?? type;
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

// Sale Order Status
export enum SaleOrderStatus {
    PENDING = 'pending',
    DELIVERED = 'delivered',
    CANCELED = 'canceled',
}

export const SALE_ORDER_STATUS_LABELS: Record<SaleOrderStatus, string> = {
    [SaleOrderStatus.PENDING]: 'Pending',
    [SaleOrderStatus.DELIVERED]: 'Delivered',
    [SaleOrderStatus.CANCELED]: 'Canceled',
};

export function saleOrderStatusLabel(status: SaleOrderStatus): string {
    return SALE_ORDER_STATUS_LABELS[status] ?? status;
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

// Payable Status
export enum PayableStatus {
    PENDING = 'pending',
    PAID = 'paid',
    CANCELED = 'canceled',
}

export const PAYABLE_STATUS_LABELS: Record<PayableStatus, string> = {
    [PayableStatus.PENDING]: 'Pending',
    [PayableStatus.PAID]: 'Paid',
    [PayableStatus.CANCELED]: 'Canceled',
};

export function payableStatusLabel(status: PayableStatus): string {
    return PAYABLE_STATUS_LABELS[status] ?? status;
}
