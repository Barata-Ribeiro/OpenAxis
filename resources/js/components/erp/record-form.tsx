import InputError from '@/components/common/input-error';
import { Button } from '@/components/ui/button';
import { FieldGroup } from '@/components/ui/field';
import { Spinner } from '@/components/ui/spinner';
import type { Auth } from '@/types/auth';
import type { Permission } from '@/types/role-permission';
import { sections, type SettingsSection } from '@/lib/erp-settings';
import { dashboard } from '@/routes';
import cash from '@/routes/erp/cash-flow';
import payables from '@/routes/erp/cash-flow/payables';
import { index as inventory } from '@/routes/erp/inventory';
import movements from '@/routes/erp/inventory/movements';
import products from '@/routes/erp/inventory/products';
import { Form } from '@inertiajs/react';
import {
    CheckboxField,
    CheckboxList,
    fieldError,
    InputField,
    scaleFields,
    scaledInput,
    SelectField,
    TextareaField,
    type FormErrors,
    type FormValues,
    type Option,
} from './form-fields';
import { ErpDate, ErpFee, ErpMoney } from './format';
import { ProductFields, useProductImages, type ProductFormData, type ProductImage } from './product-fields';

type Value = string | number | boolean | number[] | string[] | null;
type Detail =
    | string
    | number
    | null
    | { date: string; withTime?: boolean }
    | { cents: number }
    | { feeRate: number; feeType: string };
type Kind = 'product' | 'stock-movement' | 'receipt' | 'expense' | 'payable' | 'payment' | SettingsSection;

export type RecordProps = {
    title: string;
    kind: Kind;
    mode: 'create' | 'edit' | 'archive' | 'show';
    id?: number | null;
    values?: Record<string, Value>;
    choices?: Record<string, Option[]>;
    details?: Record<string, Detail>;
    productForm?: ProductFormData;
    images?: ProductImage[];
    self?: boolean;
};

type Field = {
    name: string;
    label: string;
    type?: 'number' | 'date' | 'datetime-local' | 'password' | 'email' | 'textarea' | 'checkbox' | 'select' | 'multi';
    required?: boolean;
    options?: Option[];
};

const description: Field = { name: 'description', label: 'Description', required: true };
const amount: Field = { name: 'amount_cents', label: 'Amount (BRL)', type: 'number', required: true };
const category: Field = { name: 'expense_category_id', label: 'Expense category', type: 'select', required: true };
const notes: Field = { name: 'notes', label: 'Notes', type: 'textarea' };
const name: Field = { name: 'name', label: 'Name', required: true };
const code: Field = { name: 'code', label: 'Code', required: true };
const active: Field = { name: 'is_active', label: 'Active', type: 'checkbox' };
const emptyImages: ProductImage[] = [];

function fieldsFor({ kind, mode, self }: RecordProps): Field[] {
    if (mode === 'archive' || mode === 'show') return [];
    const editing = mode === 'edit';

    switch (kind) {
        case 'receipt':
        case 'expense':
        case 'payment':
            return [
                ...(kind === 'payment' ? [] : [description, amount]),
                ...(kind === 'expense' ? [category] : []),
                { name: 'payment_method_id', label: 'Payment method', type: 'select', required: true },
                { name: 'occurred_at', label: 'Date and time (UTC)', type: 'datetime-local', required: true },
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
        default:
            return [];
    }
}

function operation({ kind, mode, id }: RecordProps) {
    switch (kind) {
        case 'product':
            return id == null
                ? products.store.form()
                : mode === 'archive'
                  ? products.destroy.form(id)
                  : { action: products.update.url(id), method: 'post' as const };
        case 'receipt':
            return cash.storeReceipt.form();
        case 'expense':
            return cash.storeExpense.form();
        case 'payable':
            return id == null
                ? payables.store.form()
                : mode === 'archive'
                  ? payables.destroy.form(id)
                  : payables.update.form(id);
        case 'payment':
            return id == null ? null : payables.pay.form(id);
        case 'stock-movement':
            return null;
        default: {
            const routes = sections[kind].routes;
            return id == null
                ? routes.store.form()
                : mode === 'archive'
                  ? routes.destroy.form(id)
                  : routes.update.form(id);
        }
    }
}

export function recordBreadcrumbs({ kind, title, auth }: RecordProps & { auth: Auth }) {
    const dashboardItem = { title: 'Dashboard', href: dashboard() };
    const current = { title, href: dashboard() };
    const permissions: Permission['name'][] = auth.permissions;
    switch (kind) {
        case 'product':
            return [
                dashboardItem,
                ...(permissions.includes('product.index') ? [{ title: 'Inventory', href: inventory() }] : []),
                current,
            ];
        case 'stock-movement':
            return [
                dashboardItem,
                ...(permissions.includes('stock-movement.index')
                    ? [{ title: 'Stock movements', href: movements.index() }]
                    : []),
                current,
            ];
        case 'receipt':
        case 'expense':
        case 'payable':
        case 'payment':
            return [
                dashboardItem,
                ...(permissions.some(
                    (permission) => permission === 'cash-entry.index' || permission === 'financial-account.index',
                )
                    ? [{ title: 'Cash flow', href: cash.index() }]
                    : []),
                current,
            ];
        default: {
            const section = sections[kind];
            return [
                dashboardItem,
                ...(permissions.includes((section.permission + '.index') as Permission['name'])
                    ? [{ title: section.title, href: section.routes.index() }]
                    : []),
                current,
            ];
        }
    }
}

function RecordDetails({ details, images }: Readonly<Pick<RecordProps, 'details' | 'images'>>) {
    return (
        <>
            {details && (
                <dl className="grid gap-3 sm:grid-cols-2">
                    {Object.entries(details).map(([label, value]) => (
                        <div className="min-w-0 rounded-md border p-3" key={label}>
                            <dt className="text-sm text-muted-foreground">{label}</dt>
                            <dd className="mt-1 text-sm font-medium wrap-break-word">
                                {value === null || value === '' ? (
                                    '—'
                                ) : typeof value !== 'object' ? (
                                    value
                                ) : 'date' in value ? (
                                    <ErpDate value={value.date} withTime={value.withTime} />
                                ) : 'feeRate' in value ? (
                                    <ErpFee rate={value.feeRate} type={value.feeType} />
                                ) : (
                                    <ErpMoney amount={value.cents / 100} />
                                )}
                            </dd>
                        </div>
                    ))}
                </dl>
            )}
            {images?.some((image) => image.url) && (
                <div className="mt-4 flex flex-wrap gap-3">
                    {images.map(
                        ({ url, id }, index) =>
                            url && (
                                <img
                                    key={id}
                                    className="aspect-square w-28 rounded-md border object-cover"
                                    src={url}
                                    alt={'Product image ' + (index + 1)}
                                />
                            ),
                    )}
                </div>
            )}
        </>
    );
}

function RecordField({ field, props, errors }: Readonly<{ field: Field; props: RecordProps; errors: FormErrors }>) {
    const { name, label, required, type } = field;
    const value = props.values?.[name];
    const options = field.options ?? props.choices?.[name] ?? [];
    const error = fieldError(errors, name);
    const text = typeof value === 'string' || typeof value === 'number' ? value : undefined;

    switch (type) {
        case 'textarea':
            return <TextareaField name={name} label={label} error={error} defaultValue={String(text ?? '')} />;
        case 'checkbox':
            return <CheckboxField name={name} label={label} error={error} defaultSelected={Boolean(value)} />;
        case 'select':
            return (
                <SelectField
                    name={name}
                    label={label}
                    error={error}
                    defaultValue={text}
                    options={options}
                    required={required}
                />
            );
        case 'multi':
            return (
                <CheckboxList
                    name={name}
                    label={label}
                    error={error}
                    options={options}
                    selected={Array.isArray(value) ? value : undefined}
                />
            );
        default: {
            const currency = name === 'amount_cents';
            return (
                <InputField
                    name={name}
                    label={label}
                    type={type ?? 'text'}
                    required={required}
                    error={error}
                    defaultValue={currency && typeof value === 'number' ? scaledInput(value, 2) : text}
                    min={type === 'number' ? 0 : undefined}
                    step={currency ? 0.01 : undefined}
                    autoComplete={type === 'password' ? 'new-password' : undefined}
                />
            );
        }
    }
}

export function RecordForm({
    props,
    close,
    onProcessing,
}: Readonly<{
    props: RecordProps;
    close?: () => void;
    onProcessing?: (processing: boolean) => void;
}>) {
    const upload = useProductImages(props.productForm?.images ?? emptyImages);
    if (props.mode === 'show') return <RecordDetails details={props.details} images={props.images} />;
    const target = operation(props);
    if (!target) return null;

    const fields = fieldsFor(props);
    const confirm = props.mode === 'archive';
    const deleteRecord = props.kind === 'users' || props.kind === 'roles';
    const cancelPayable = props.kind === 'payable';

    function transform(data: FormValues) {
        if (props.productForm) return { ...upload.transform(data), _method: 'PATCH' };
        return {
            ...scaleFields(data, { amount_cents: 2 }),
            ...(fields.some((field) => field.name === 'is_active') ? { is_active: data['is_active'] === '1' } : {}),
            ...(props.kind === 'payment'
                ? { expected_amount_cents: Number(props.values?.['expected_amount_cents']) }
                : {}),
        };
    }

    return (
        <Form
            {...target}
            transform={transform}
            className="space-y-5"
            disableWhileProcessing
            options={{ preserveScroll: true }}
            onStart={() => onProcessing?.(true)}
            onFinish={() => onProcessing?.(false)}
            onSuccess={close}
        >
            {({ errors, processing, progress }) => (
                <>
                    <RecordDetails details={props.details} />
                    {confirm && (
                        <p className="text-sm text-muted-foreground">
                            {cancelPayable
                                ? 'Cancel this unpaid obligation?'
                                : deleteRecord
                                  ? 'Permanently delete this record?'
                                  : 'Archive this record?'}
                        </p>
                    )}
                    {props.productForm ? (
                        <ProductFields
                            data={props.productForm}
                            errors={errors}
                            upload={upload}
                            uploadProgress={progress?.percentage}
                        />
                    ) : (
                        fields.length > 0 && (
                            <FieldGroup className="grid gap-4 sm:grid-cols-2">
                                {fields.map((field) => (
                                    <RecordField key={field.name} field={field} props={props} errors={errors} />
                                ))}
                            </FieldGroup>
                        )
                    )}
                    <InputError role="alert" message={errors['record']} />
                    <Button type="submit" variant={confirm ? 'destructive' : 'default'} isDisabled={processing}>
                        {processing && <Spinner />}
                        {confirm
                            ? cancelPayable
                                ? 'Cancel payable'
                                : deleteRecord
                                  ? 'Delete'
                                  : 'Archive'
                            : props.kind === 'payment'
                              ? 'Confirm payment'
                              : 'Save'}
                    </Button>
                </>
            )}
        </Form>
    );
}
