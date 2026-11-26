import InputError from '@/components/common/input-error';
import { CheckboxField, CheckboxList, InputField, SelectField, TextareaField } from '@/components/erp/forms/fields';
import { Button } from '@/components/ui/button';
import { FieldGroup } from '@/components/ui/field';
import { Spinner } from '@/components/ui/spinner';
import { fieldError, scaledInput, scaleFields } from '@/lib/erp/forms';
import type { EditFormProps, FieldDefinition, FormErrors, FormTarget, FormValues } from '@/types/erp/forms';
import { Form } from '@inertiajs/react';
import type { ReactNode } from 'react';

import { RecordScreen } from '../records/record-screen';

function RecordField({
    field,
    props,
    errors,
}: Readonly<{ field: FieldDefinition; props: EditFormProps; errors: FormErrors }>) {
    const { name, label, required, type, precision } = field;
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
            return (
                <InputField
                    name={name}
                    label={label}
                    type={type ?? 'text'}
                    required={required}
                    error={error}
                    defaultValue={
                        precision !== undefined && typeof value === 'number' ? scaledInput(value, precision) : text
                    }
                    min={type === 'number' ? 0 : undefined}
                    step={precision === undefined ? undefined : 10 ** -precision}
                    autoComplete={type === 'password' ? 'new-password' : undefined}
                />
            );
        }
    }
}

export function FormEditor({
    props,
    target,
    fields,
    children,
    hidden,
    submitLabel = 'Save',
}: Readonly<{
    props: EditFormProps;
    target: FormTarget;
    fields: readonly FieldDefinition[];
    children?: ReactNode;
    hidden?: Record<string, string | number>;
    submitLabel?: string;
}>) {
    function transform(data: FormValues) {
        const scaled = scaleFields(
            data,
            Object.fromEntries(
                fields.flatMap(({ name, precision }) => (precision === undefined ? [] : [[name, precision]])),
            ),
        );

        for (const { name, type } of fields) {
            if (type === 'checkbox') scaled[name] = data[name] === '1';
        }

        return { ...scaled, ...hidden };
    }

    return (
        <RecordScreen {...props} description="Complete the fields below">
            {({ close, setProcessing }) => (
                <Form
                    {...target}
                    transform={transform}
                    className="space-y-5"
                    disableWhileProcessing
                    options={{ preserveScroll: true }}
                    onStart={() => setProcessing(true)}
                    onFinish={() => setProcessing(false)}
                    onSuccess={close}
                >
                    {({ errors, processing }) => (
                        <>
                            {children}
                            <FieldGroup className="grid gap-4 sm:grid-cols-2">
                                {fields.map((field) => (
                                    <RecordField key={field.name} field={field} props={props} errors={errors} />
                                ))}
                            </FieldGroup>
                            <InputError role="alert" message={errors['record']} />
                            <Button type="submit" isDisabled={processing}>
                                {processing && <Spinner />}
                                {submitLabel}
                            </Button>
                        </>
                    )}
                </Form>
            )}
        </RecordScreen>
    );
}
