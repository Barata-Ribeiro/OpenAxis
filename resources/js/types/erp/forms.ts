export type Option = { id: number | string; name: string };
export type FormValues = Record<string, string | string[] | number | number[] | boolean | File[] | null | undefined>;
export type FormErrors = Record<string, string | undefined>;
type FieldValue = Exclude<FormValues[string], File[] | undefined>;

export type FieldDefinition = {
    name: string;
    label: string;
    type?: 'number' | 'date' | 'datetime-local' | 'password' | 'email' | 'textarea' | 'checkbox' | 'select' | 'multi';
    required?: boolean;
    options?: Option[];
    precision?: number;
};

export type EditFormProps = {
    title: string;
    modal?: boolean;
    values?: Record<string, FieldValue>;
    choices?: Record<string, Option[]>;
};

export type FormTarget = { action: string; method: 'post' };
