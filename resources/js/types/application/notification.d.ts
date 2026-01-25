import type { InventoryMovementType } from '@/types/erp/erp-enums';

export interface Notification<T = Record<string, unknown>> {
    id: string;
    type: string;
    notifiable_type: string;
    notifiable_id: number;
    data: { message: string; type: string } & T;
    read_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface NotificationSummary<T = Record<string, unknown>> {
    latest: Notification<T>[];
    unread_count: number;
    total_count: number;
}

// Notifications Data
export interface ManualSupplyAdjustmentData {
    product: string;
    movement_type: InventoryMovementType;
    quantity: number;
    reason: string | null;
}

export interface NewPurchaseOrderData {
    purchase_order_id: number;
    order_number: string;
    order_date: string;
    total_cost: number;
    supplier: string;
    created_by: string;
}

export interface NewSalesOrderData {
    sales_order_id: number;
    order_number: string;
    order_date: string;
    total_cost: number;
    client: string;
    vendor: string;
    created_by: string;
}
