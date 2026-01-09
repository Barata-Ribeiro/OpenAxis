import { Field, FieldLabel } from '@/components/ui/field';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { PaymentCondition } from '@/types/erp/payment-condition';
import { usePage } from '@inertiajs/react';

interface PaymentConditionSelectorProps {
    errors?: string;
}

export default function PaymentConditionSelector({ errors }: Readonly<PaymentConditionSelectorProps>) {
    const { paymentConditions } = usePage<{ paymentConditions: PaymentCondition[] }>().props;

    return (
        <Field data-invalid={!!errors}>
            <FieldLabel htmlFor="payment_condition_id">Payment Condition</FieldLabel>
            <Select name="payment_condition_id" required aria-required>
                <SelectTrigger className="w-full" aria-invalid={!!errors}>
                    <SelectValue placeholder="Select Payment Condition" />
                </SelectTrigger>
                <SelectContent>
                    {paymentConditions.map((condition) => (
                        <SelectItem key={condition.id} value={condition.id.toString()}>
                            {condition.name}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </Field>
    );
}
