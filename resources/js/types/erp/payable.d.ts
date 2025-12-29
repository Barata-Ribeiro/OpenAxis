import type { User } from '@/types/application/user';
import type { PayableStatus } from '@/types/erp/erp-enums';
import type { Partner } from '@/types/erp/partner';
import type { SaleOrder } from '@/types/erp/sale-order';
import type { Vendor } from '@/types/erp/vendor';

type PaymentMethod = 'bank_transfer' | 'credit_card' | 'cash' | 'check';

export interface Payable {
    id: number;
    code: string;
    dsecription: string;
    supplier_id: number;
    vendor_id: number;
    amount: number;
    issue_date: string;
    due_date: string;
    payment_date: string | null;
    status: PayableStatus;
    payment_method: PaymentMethod;
    bank_account_id: number;
    sales_order_id: number;
    reference_number: string | null;
    notes: string | null;
    user_id: number;
    created_at: string;
    updated_at: string;
}

export interface PayableWithRelations extends Payable {
    supplier: Partner;
    vendor: Vendor;
    bank_account: unknown; // TODO: Replace 'unknown' with actual BankAccount type when available
    sales_order: SaleOrder;
    user: User;
}
