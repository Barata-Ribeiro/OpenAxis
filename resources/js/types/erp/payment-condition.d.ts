export interface PaymentCondition {
    id: number;
    code: string;
    name: string;
    days_until_due: number;
    installments: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}
