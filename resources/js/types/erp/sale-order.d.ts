import type { SaleOrderStatus } from '@/types/erp/erp-enums';
import type { Partner } from '@/types/erp/partner';
import type { PaymentCondition } from '@/types/erp/payment-condition';
import type { VendorWithRelations } from '@/types/erp/vendor';

type PaymentMethod = 'cash' | 'credit_card' | 'debit_card' | 'bank_transfer';

export interface SaleOrder {
    id: number;
    client_id: number;
    vendor_id: number;
    order_number: string;
    order_date: string;
    delivery_date: string;
    status: SaleOrderStatus;
    product_cost: number;
    delivery_cost: number;
    discount_cost: number;
    total_cost: number;
    product_value: number;
    total_commission: number;
    payment_method: PaymentMethod;
    notes: string | null;
    user_id: number;
    created_at: string;
    updated_at: string;
    payment_condition_id: number;
}

export interface SaleOrderWithRelations extends SaleOrder {
    client: Partner;
    vendor: VendorWithRelations;
    user: Use;
    payment_condition: PaymentCondition;
}
