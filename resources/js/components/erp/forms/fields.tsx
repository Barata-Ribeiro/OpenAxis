import InputError from '@/components/common/input-error';
import { Checkbox } from '@/components/ui/checkbox';
import { Field, FieldLegend, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { ComponentProps } from 'react';
import { useId } from 'react';

import type { Option } from '@/types/erp/forms';

type InputFieldProps = ComponentProps<typeof Input> & { label: string; name: string; error?: string };

export function InputField({ label, name, error, ...props }: Readonly<InputFieldProps>) {
    const id = useId();
    return (
        <Field data-invalid={Boolean(error)}>
            <Label htmlFor={id}>{label}</Label>
            <Input
                {...props}
                id={id}
                name={name}
                aria-invalid={Boolean(error)}
                aria-describedby={error ? id + '-error' : undefined}
            />
            <InputError id={id + '-error'} message={error} />
        </Field>
    );
}

export function TextareaField({
    label,
    name,
    error,
    defaultValue,
}: Readonly<{
    label: string;
    name: string;
    error?: string;
    defaultValue?: string;
}>) {
    const id = useId();
    return (
        <Field data-invalid={Boolean(error)}>
            <Label htmlFor={id}>{label}</Label>
            <Textarea
                id={id}
                name={name}
                defaultValue={defaultValue}
                aria-invalid={Boolean(error)}
                aria-describedby={error ? id + '-error' : undefined}
            />
            <InputError id={id + '-error'} message={error} />
        </Field>
    );
}

export function SelectField({
    label,
    name,
    options,
    defaultValue,
    required,
    error,
}: Readonly<{
    label: string;
    name: string;
    options: Option[];
    defaultValue?: string | number | null;
    required?: boolean;
    error?: string;
}>) {
    const id = useId();
    return (
        <Field data-invalid={Boolean(error)}>
            <Select
                className="grid w-full gap-2"
                name={name}
                defaultValue={defaultValue == null || defaultValue === '' ? null : String(defaultValue)}
                isRequired={required}
                isInvalid={Boolean(error)}
                aria-describedby={error ? id + '-error' : undefined}
            >
                <Label>{label}</Label>
                <SelectTrigger>
                    <SelectValue />
                </SelectTrigger>
                <SelectContent>
                    {!required && <SelectItem id="__none__">None</SelectItem>}
                    {options.map((option) => (
                        <SelectItem key={option.id} id={String(option.id)}>
                            {option.name}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
            <InputError id={id + '-error'} message={error} />
        </Field>
    );
}

export function CheckboxField({
    label,
    name,
    defaultSelected,
    error,
}: Readonly<{
    label: string;
    name: string;
    defaultSelected?: boolean;
    error?: string;
}>) {
    const id = useId();
    return (
        <Field data-invalid={Boolean(error)}>
            <div className="flex items-center gap-2">
                <Checkbox
                    id={id}
                    name={name}
                    value="1"
                    defaultSelected={defaultSelected}
                    aria-label={label}
                    aria-describedby={error ? id + '-error' : undefined}
                    isInvalid={Boolean(error)}
                />
                <Label htmlFor={id}>{label}</Label>
            </div>
            <InputError id={id + '-error'} message={error} />
        </Field>
    );
}

export function CheckboxList({
    label,
    name,
    options,
    selected = [],
    error,
}: Readonly<{
    label: string;
    name: string;
    options: Option[];
    selected?: (number | string)[];
    error?: string;
}>) {
    const id = useId();
    return (
        <FieldSet aria-invalid={Boolean(error)} aria-describedby={error ? id + '-error' : undefined}>
            <FieldLegend variant="label">{label}</FieldLegend>
            <div className="max-h-40 space-y-2 overflow-y-auto rounded-md border p-3">
                {options.length === 0 && <p className="text-sm text-muted-foreground">No options available.</p>}
                {options.map((option) => {
                    const optionId = id + '-' + option.id;
                    return (
                        <div key={option.id} className="flex items-center gap-2">
                            <Checkbox
                                id={optionId}
                                name={name + '[]'}
                                value={String(option.id)}
                                defaultSelected={selected.some((value) => String(value) === String(option.id))}
                                aria-label={option.name}
                            />
                            <Label htmlFor={optionId}>{option.name}</Label>
                        </div>
                    );
                })}
            </div>
            <InputError id={id + '-error'} message={error} />
        </FieldSet>
    );
}
