import type { FieldDefinition } from '@/types/erp/forms';

const description = { name: 'description', label: 'Description', required: true } satisfies FieldDefinition;
const amount = {
    name: 'amount_cents',
    label: 'Amount (BRL)',
    type: 'number',
    required: true,
    precision: 2,
} satisfies FieldDefinition;
const category = {
    name: 'expense_category_id',
    label: 'Expense category',
    type: 'select',
    required: true,
} satisfies FieldDefinition;
const notes = { name: 'notes', label: 'Notes', type: 'textarea' } satisfies FieldDefinition;

export const paymentFields = [
    { name: 'payment_method_id', label: 'Payment method', type: 'select', required: true },
    { name: 'occurred_at', label: 'Date and time (UTC)', type: 'datetime-local', required: true },
    notes,
] satisfies FieldDefinition[];

export const receiptFields = [description, amount, ...paymentFields];
export const expenseFields = [description, amount, category, ...paymentFields];
export const payableFields = [
    description,
    category,
    amount,
    { name: 'due_on', label: 'Due date', type: 'date', required: true },
    { name: 'contact_name', label: 'Contact name' },
    { name: 'contact_phone', label: 'Contact phone' },
    notes,
] satisfies FieldDefinition[];
