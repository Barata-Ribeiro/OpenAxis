import { PurchaseOrderStatus } from '../application/enums';
import type { User } from '../application/user';
import type { Partner } from './partner';

export interface PurchaseOrder {
    id: number;
    order_number: string;
    order_date: string;
    forecast_date: string;
    status: PurchaseOrderStatus;
    total_cost: number;
    notes: string | null;
    supplier_id: number;
    user_id: number;
    created_at: string;
    updated_at: string;
}

export interface PurchaseOrderWithRelations extends PurchaseOrder {
    supplier: Partner;
    user: User;
}
