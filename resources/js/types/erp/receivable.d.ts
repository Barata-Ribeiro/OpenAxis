import type { User } from '@/types/application/user';
import type { ReceivableStatus } from '@/types/erp/erp-enums';
import type { Partner } from '@/types/erp/partner';
import type { SaleOrder } from '@/types/erp/sale-order';

type PaymentMethod = 'bank_transfer' | 'credit_card' | 'cash' | 'check';

export interface Receivable {
    id: number;
    code: string;
    description: string;
    client_id: number;
    amount: number;
    issue_date: string;
    due_date: string;
    received_date: string | null;
    status: ReceivableStatus;
    payment_method: PaymentMethod;
    bank_account_id: number;
    sales_order_id: number;
    reference_number: string | null;
    notes: string | null;
    user_id: number;
    created_at: string;
    updated_at: string;
}

export interface ReceivableWithRelations extends Receivable {
    client: Partner;
    bank_account: unknown; // TODO: Replace 'unknown' with actual BankAccount type when available
    sales_order: SaleOrder;
    user: User;
}
