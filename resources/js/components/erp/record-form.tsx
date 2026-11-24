import InputError from '@/components/common/input-error';
import { ErpLink } from '@/components/erp/page';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { usePermission } from '@/hooks/use-permission';
import { sections, type SettingsSection } from '@/lib/erp-settings';
import { dashboard } from '@/routes';
import cash from '@/routes/erp/cash-flow';
import payables from '@/routes/erp/cash-flow/payables';
import { index as inventory } from '@/routes/erp/inventory';
import movements from '@/routes/erp/inventory/movements';
import products from '@/routes/erp/inventory/products';
import type { Permission } from '@/types/role-permission';
import { useForm } from '@inertiajs/react';
import type { SubmitEvent } from 'react';
import { Activity, useId } from 'react';

type Value = string | number | boolean | (number | string)[];
type Option = { id: number | string; name: string };
type Fields = Record<string, Value | null>;
type Kind =
    | 'product'
    | 'stock-movement'
    | 'stock-receipt'
    | 'stock-issue'
    | 'receipt'
    | 'expense'
    | 'payable'
    | 'payment'
    | SettingsSection;
export type RecordProps = {
    title: string;
    kind: Kind;
    mode: 'create' | 'edit' | 'archive' | 'show';
    id?: number;
    values?: Fields;
    choices?: Record<string, Option[]>;
    details?: Record<string, string | number | null>;
    self?: boolean;
};

type Field = {
    name: string;
    label: string;
    type?: 'number' | 'date' | 'datetime-local' | 'password' | 'email' | 'textarea' | 'checkbox' | 'select' | 'multi';
    required?: boolean;
    decimals?: number;
    options?: Option[];
};

const location: Field = { name: 'storage_location_id', label: 'Storage location', type: 'select' };
const category: Field = { name: 'expense_category_id', label: 'Expense category', type: 'select', required: true };
const description: Field = { name: 'description', label: 'Description', required: true };
const amount: Field = { name: 'amount_cents', label: 'Amount (BRL)', type: 'number', decimals: 2, required: true };
const occurred: Field = { name: 'occurred_at', label: 'Date and time (UTC)', type: 'datetime-local', required: true };
const notes: Field = { name: 'notes', label: 'Notes', type: 'textarea' };
const active: Field = { name: 'is_active', label: 'Active', type: 'checkbox' };
const name: Field = { name: 'name', label: 'Name', required: true };
const code: Field = { name: 'code', label: 'Code', required: true };

function fieldsFor({ kind, mode, self }: RecordProps): Field[] {
    const editing = mode === 'edit';
    if (mode === 'show' || mode === 'archive') return [];

    switch (kind) {
        case 'product':
            return [
                ...(editing
                    ? []
                    : ([
                          { name: 'sku', label: 'SKU', required: true },
                          { name: 'unit_of_measure_id', label: 'Unit of measure', type: 'select', required: true },
                      ] satisfies Field[])),
                name,
                { ...description, type: 'textarea', required: false },
                location,
                { name: 'category_ids', label: 'Product categories', type: 'multi' },
                {
                    name: 'cost_price_cents',
                    label: 'Reference unit cost (BRL)',
                    type: 'number',
                    decimals: 2,
                    required: true,
                },
                { name: 'sale_price_cents', label: 'Sale price (BRL)', type: 'number', decimals: 2, required: true },
                {
                    name: 'minimum_stock_milliunits',
                    label: 'Minimum stock',
                    type: 'number',
                    decimals: 3,
                    required: true,
                },
                active,
            ];
        case 'stock-receipt':
        case 'stock-issue':
            return [
                kind === 'stock-receipt'
                    ? { name: 'product_id', label: 'Product', type: 'select', required: true }
                    : { name: 'product_batch_id', label: 'Batch', type: 'select', required: true },
                { ...location, required: true },
                ...(kind === 'stock-receipt'
                    ? ([
                          { name: 'supplier_lot_code', label: 'Supplier lot code' },
                          { name: 'expires_on', label: 'Expiry date', type: 'date' },
                          { name: 'cost_cents', label: 'Total acquisition cost (BRL)', type: 'number', decimals: 2 },
                      ] satisfies Field[])
                    : []),
                { name: 'quantity_milliunits', label: 'Quantity', type: 'number', decimals: 3, required: true },
                occurred,
                notes,
            ];
        case 'receipt':
        case 'expense':
        case 'payment':
            return [
                ...(kind === 'payment' ? [] : [description, amount]),
                ...(kind === 'expense' ? [category] : []),
                { name: 'payment_method_id', label: 'Payment method', type: 'select', required: true },
                occurred,
                notes,
            ];
        case 'payable':
            return [
                description,
                category,
                amount,
                { name: 'due_on', label: 'Due date', type: 'date', required: true },
                { name: 'contact_name', label: 'Contact name' },
                { name: 'contact_phone', label: 'Contact phone' },
                notes,
            ];
        case 'payment-methods':
            return [
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
            ];
        case 'categories':
            return [name, { name: 'parent_id', label: 'Parent category', type: 'select' }];
        case 'units':
            return editing
                ? [name]
                : [
                      name,
                      code,
                      { name: 'quantity_decimals', label: 'Quantity decimals', type: 'number', required: true },
                  ];
        case 'locations':
            return [name, code, active];
        case 'users':
            return [
                name,
                { name: 'email', label: 'Email', type: 'email', required: true },
                { name: 'password', label: 'Password', type: 'password', required: !editing },
                { name: 'password_confirmation', label: 'Confirm password', type: 'password', required: !editing },
                ...(self ? [] : [{ name: 'role_ids', label: 'Roles', type: 'multi' } satisfies Field]),
            ];
        case 'roles':
            return [name, { name: 'permission_ids', label: 'Permissions', type: 'multi' }];
        case 'expense-categories':
            return [name];
        case 'stock-movement':
            return [];
    }
}

function operation({ kind, mode, id }: RecordProps) {
    if (mode === 'show') return undefined;
    if (!id) return undefined;

    switch (kind) {
        case 'product': {
            const productOperationResult = mode === 'archive' ? products.destroy(id) : products.update(id);
            return id === undefined ? products.store() : productOperationResult;
        }
        case 'stock-receipt':
            return movements.storeReceipt();
        case 'stock-issue':
            return movements.storeIssue();
        case 'receipt':
            return cash.storeReceipt();
        case 'expense':
            return cash.storeExpense();
        case 'payable': {
            const updatedOrArchivedPayable = mode === 'archive' ? payables.destroy(id) : payables.update(id);
            return id === undefined ? payables.store() : updatedOrArchivedPayable;
        }
        case 'payment':
            return payables.pay(id!);
        case 'stock-movement':
            return undefined;
        default: {
            const routes = sections[kind].routes;
            const archivedRoute = mode === 'archive' ? routes.destroy(id) : routes.update(id);
            return id === undefined ? routes.store() : archivedRoute;
        }
    }
}

function backLink(kind: Kind): { href: string; permissions: Permission['name'][] } {
    switch (kind) {
        case 'product':
            return { href: inventory.url(), permissions: ['product.index'] };
        case 'stock-movement':
        case 'stock-receipt':
        case 'stock-issue':
            return { href: movements.index.url(), permissions: ['stock-movement.index'] };
        case 'receipt':
        case 'expense':
        case 'payable':
        case 'payment':
            return { href: cash.index.url(), permissions: ['cash-entry.index', 'financial-account.index'] };
        default:
            return { href: sections[kind].routes.index.url(), permissions: [`${sections[kind].permission}.index`] };
    }
}

export function RecordBack({ kind }: Readonly<{ kind: Kind }>) {
    const { can } = usePermission();
    const { href, permissions } = backLink(kind);
    return <ErpLink href={can(...permissions) ? href : dashboard.url()}>Back</ErpLink>;
}

export function RecordDetails({ details }: Readonly<Pick<RecordProps, 'details'>>) {
    if (!details) return null;
    return (
        <dl className="grid gap-3 sm:grid-cols-2">
            {Object.entries(details).map(([label, value]) => (
                <div className="min-w-0 rounded-md border p-3" key={label}>
                    <dt className="text-sm text-muted-foreground">{label}</dt>
                    <dd className="mt-1 text-sm font-medium wrap-break-word">
                        {value === null || value === '' ? '—' : value}
                    </dd>
                </div>
            ))}
        </dl>
    );
}

export function useRecordForm(props: RecordProps) {
    const fields = fieldsFor(props);
    const form = useForm<Fields>(
        Object.fromEntries(
            fields.map((field) => {
                const value = props.values?.[field.name];
                const fieldDefault = field.type === 'checkbox' ? false : '';
                const initial = value ?? (field.type === 'multi' ? [] : fieldDefault);
                return [
                    field.name,
                    field.decimals && typeof value === 'number'
                        ? (value / 10 ** field.decimals).toFixed(field.decimals)
                        : initial,
                ];
            }),
        ),
    );

    function submit(event: SubmitEvent<HTMLFormElement>, close?: () => void) {
        event.preventDefault();
        const target = operation(props);
        if (!target) return;

        form.transform((data) => {
            const payload = { ...data };
            if (props.kind === 'payment')
                payload['expected_amount_cents'] = props.values?.['expected_amount_cents'] ?? null;
            for (const field of fields) {
                const value = payload[field.name];
                if (field.decimals && typeof value === 'string' && value !== '') {
                    payload[field.name] = Math.round(Number(value) * 10 ** field.decimals);
                }
            }
            return payload;
        });
        form.submit(target.method, target.url, { preserveScroll: true, onSuccess: close });
    }

    return { fields, form, submit };
}

export function RecordForm({
    props,
    model,
    close,
}: Readonly<{
    props: RecordProps;
    model: ReturnType<typeof useRecordForm>;
    close?: () => void;
}>) {
    const prefix = useId();
    const { form, fields, submit } = model;
    if (props.mode === 'show') return <RecordDetails details={props.details} />;

    const confirm = props.mode === 'archive';
    const payment = props.kind === 'payment';
    const deleteRecord = props.kind === 'users' || props.kind === 'roles';

    const confirmationMessage = deleteRecord ? 'Permanently delete this record?' : 'Archive this record?';
    const submitButtonLabel = payment ? 'Confirm payment' : 'Save';
    const recordAction = deleteRecord ? 'Delete' : 'Archive';
    const cancelOrArchiveAction = props.kind === 'payable' ? 'Cancel payable' : recordAction;

    return (
        <form className="space-y-4" onSubmit={(event) => submit(event, close)}>
            <RecordDetails details={props.details} />
            <Activity mode={confirm ? 'visible' : 'hidden'}>
                <p className="text-sm text-muted-foreground">
                    {props.kind === 'payable' ? 'Cancel this unpaid obligation?' : confirmationMessage}
                </p>
            </Activity>
            <div className="grid gap-4 sm:grid-cols-2">
                {fields.map((field) => {
                    const id = prefix + '-' + field.name;
                    const value = form.data[field.name];
                    const options = field.options ?? props.choices?.[field.name] ?? [];
                    const error =
                        form.errors[field.name] ??
                        Object.entries(form.errors).find(([key]) => key.startsWith(field.name + '.'))?.[1];
                    const common = {
                        id,
                        required: field.required,
                        'aria-invalid': Boolean(error),
                        'aria-describedby': error ? id + '-error' : undefined,
                    };
                    const selectClass =
                        'w-full rounded-md border border-input bg-background p-2 text-sm focus-visible:outline-2 focus-visible:outline-ring';
                    const input = (() => {
                        switch (field.type) {
                            case 'textarea':
                                return (
                                    <Textarea
                                        {...common}
                                        value={String(value ?? '')}
                                        onChange={(event) => form.setData(field.name, event.target.value)}
                                    />
                                );
                            case 'checkbox':
                                return (
                                    <input
                                        {...common}
                                        type="checkbox"
                                        checked={Boolean(value)}
                                        onChange={(event) => form.setData(field.name, event.target.checked)}
                                    />
                                );
                            case 'select':
                                return (
                                    <select
                                        {...common}
                                        className={selectClass}
                                        value={String(value ?? '')}
                                        onChange={(event) => form.setData(field.name, event.target.value)}
                                    >
                                        <option value="">{field.required ? 'Select an option' : 'None'}</option>
                                        {options.map((option) => (
                                            <option key={option.id} value={option.id}>
                                                {option.name}
                                            </option>
                                        ))}
                                    </select>
                                );
                            case 'multi':
                                return (
                                    <div
                                        id={id}
                                        className="max-h-40 space-y-1 overflow-auto rounded-md border p-2"
                                        role="group"
                                        aria-labelledby={id + '-label'}
                                        aria-describedby={common['aria-describedby']}
                                    >
                                        {options.length === 0 && (
                                            <p className="text-sm text-muted-foreground">No options available.</p>
                                        )}
                                        {options.map((option) => {
                                            const selected = Array.isArray(value) ? value.map(String) : [];
                                            const key = String(option.id);
                                            return (
                                                <label key={key} className="flex items-center gap-2 text-sm">
                                                    <input
                                                        type="checkbox"
                                                        checked={selected.includes(key)}
                                                        onChange={(event) =>
                                                            form.setData(
                                                                field.name,
                                                                event.target.checked
                                                                    ? [...selected, key]
                                                                    : selected.filter((item) => item !== key),
                                                            )
                                                        }
                                                    />
                                                    {option.name}
                                                </label>
                                            );
                                        })}
                                    </div>
                                );
                            default:
                                return (
                                    <Input
                                        {...common}
                                        type={field.type ?? 'text'}
                                        value={String(value ?? '')}
                                        min={field.type === 'number' ? 0 : undefined}
                                        step={field.decimals ? 1 / 10 ** field.decimals : undefined}
                                        autoComplete={field.type === 'password' ? 'new-password' : undefined}
                                        onChange={(event) => form.setData(field.name, event.target.value)}
                                    />
                                );
                        }
                    })();
                    return (
                        <div
                            className={field.type === 'checkbox' ? 'flex items-center gap-2' : 'space-y-1'}
                            key={field.name}
                        >
                            {field.type === 'multi' ? (
                                <p className="text-sm font-medium" id={id + '-label'}>
                                    {field.label}
                                </p>
                            ) : (
                                <Label htmlFor={id}>{field.label}</Label>
                            )}
                            {input}
                            <InputError id={id + '-error'} message={error} />
                        </div>
                    );
                })}
            </div>
            <InputError role="alert" message={form.errors['record']} />
            <Button type="submit" variant={confirm ? 'destructive' : 'default'} isDisabled={form.processing}>
                {confirm ? cancelOrArchiveAction : submitButtonLabel}
            </Button>
        </form>
    );
}
