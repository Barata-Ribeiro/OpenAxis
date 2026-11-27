import type { FieldDefinition } from '@/types/erp/forms';

const name = { name: 'name', label: 'Name', required: true } satisfies FieldDefinition;
const code = { name: 'code', label: 'Code', required: true } satisfies FieldDefinition;
const active = { name: 'is_active', label: 'Active', type: 'checkbox' } satisfies FieldDefinition;

export const nameFields = [name];
export const paymentMethodFields = [
    name,
    {
        name: 'fee_type',
        label: 'Fee type',
        type: 'select',
        required: true,
        options: [
            { id: 'fixed', name: 'Fixed' },
            { id: 'percentage', name: 'Percentage' },
        ],
    },
    { name: 'fee_rate', label: 'Fee rate (cents or basis points)', type: 'number', required: true },
    active,
] satisfies FieldDefinition[];
export const categoryFields = [
    name,
    { name: 'parent_id', label: 'Parent category', type: 'select' },
] satisfies FieldDefinition[];
export const unitFields = [
    name,
    code,
    { name: 'quantity_decimals', label: 'Quantity decimals', type: 'number', required: true },
] satisfies FieldDefinition[];
export const locationFields = [name, code, active];
export const roleFields = [
    name,
    { name: 'permission_ids', label: 'Permissions', type: 'multi' },
] satisfies FieldDefinition[];

export function userFields(editing: boolean, self = false): FieldDefinition[] {
    return [
        name,
        { name: 'email', label: 'Email', type: 'email', required: true },
        { name: 'password', label: 'Password', type: 'password', required: !editing },
        { name: 'password_confirmation', label: 'Confirm password', type: 'password', required: !editing },
        ...(!self ? [{ name: 'role_ids', label: 'Roles', type: 'multi' } satisfies FieldDefinition] : []),
    ];
}
