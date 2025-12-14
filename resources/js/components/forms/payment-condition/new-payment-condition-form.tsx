import InputError from '@/components/feedback/input-error';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Checkbox } from '@/components/ui/checkbox';
import { Field, FieldGroup, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from '@/components/ui/input-group';
import { Spinner } from '@/components/ui/spinner';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import erp from '@/routes/erp';
import { Form, Link } from '@inertiajs/react';
import { HelpCircle } from 'lucide-react';
import { Activity } from 'react';

export default function NewPaymentConditionForm() {
    return (
        <Form
            {...erp.paymentConditions.store.form()}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
        >
            {({ processing, resetAndClearErrors, errors }) => (
                <>
                    <FieldGroup className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <Field aria-invalid={!!errors.code}>
                            <FieldLabel htmlFor="code">Code</FieldLabel>
                            <InputGroup>
                                <InputGroupInput
                                    type="text"
                                    id="code"
                                    name="code"
                                    placeholder="e.g. PC50"
                                    maxLength={20}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.code}
                                />
                                <InputGroupAddon align="inline-end">
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <InputGroupButton variant="ghost" aria-label="Help" size="icon-xs">
                                                <HelpCircle aria-hidden />
                                            </InputGroupButton>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>A unique code to identify the payment condition.</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </InputGroupAddon>
                            </InputGroup>
                            <InputError message={errors.code} />
                        </Field>
                        <Field aria-invalid={!!errors.name}>
                            <FieldLabel htmlFor="name">Name</FieldLabel>
                            <Input
                                type="text"
                                id="name"
                                name="name"
                                placeholder="e.g. Cash, 50% upfront"
                                maxLength={100}
                                required
                                aria-required
                                aria-invalid={!!errors.name}
                            />
                            <InputError message={errors.name} />
                        </Field>
                    </FieldGroup>

                    <FieldGroup className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <Field aria-invalid={!!errors.days_until_due}>
                            <FieldLabel htmlFor="days_until_due">Days Until Due</FieldLabel>
                            <Input
                                type="number"
                                id="days_until_due"
                                name="days_until_due"
                                placeholder="e.g. 30"
                                min={0}
                                required
                                aria-required
                                aria-invalid={!!errors.days_until_due}
                            />
                            <InputError message={errors.days_until_due} />
                        </Field>

                        <Field aria-invalid={!!errors.installments}>
                            <FieldLabel htmlFor="installments">Installments</FieldLabel>
                            <InputGroup>
                                <InputGroupInput
                                    type="number"
                                    id="installments"
                                    name="installments"
                                    placeholder="e.g. 1"
                                    min={1}
                                    required
                                    aria-required
                                    aria-invalid={!!errors.installments}
                                />

                                <InputGroupAddon align="inline-end">
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <InputGroupButton variant="ghost" aria-label="Help" size="icon-xs">
                                                <HelpCircle aria-hidden />
                                            </InputGroupButton>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>The number of installments to split the payment into.</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </InputGroupAddon>
                            </InputGroup>
                            <InputError message={errors.installments} />
                        </Field>
                    </FieldGroup>

                    <Field data-invalid={!!errors.is_active}>
                        <div className="flex items-center space-x-3">
                            <Checkbox id="is_active" name="is_active" aria-invalid={!!errors.is_active} />
                            <FieldLabel htmlFor="is_active">Set as active</FieldLabel>
                        </div>
                        <InputError message={errors.is_active} />
                    </Field>

                    <ButtonGroup>
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.paymentConditions.index()} prefetch="hover" as="button">
                                Cancel
                            </Link>
                        </Button>
                        <ButtonGroup>
                            <Button type="submit" disabled={processing}>
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner />
                                </Activity>
                                Save
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => resetAndClearErrors()}
                                disabled={processing}
                            >
                                Reset
                            </Button>
                        </ButtonGroup>
                    </ButtonGroup>
                </>
            )}
        </Form>
    );
}
