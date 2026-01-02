import type { BankAccountType } from '@/types/erp/erp-enums';

interface BankAccount {
    id: number;
    name: string;
    type: BankAccountType;
    bank_name: string;
    bank_agency: string;
    bank_account_number: string;
    pix_key: string | null; // Key for PIX transactions, if applicable.
    destination_name: string; // Name of the account holder
    initial_balance: number;
    current_balance: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}
