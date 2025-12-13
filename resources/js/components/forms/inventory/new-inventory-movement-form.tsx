import InputError from '@/components/feedback/input-error';
import NewInventoryInventorySelectCombobox from '@/components/helpers/inventory/inventory-select-combobox';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Field, FieldLabel } from '@/components/ui/field';
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from '@/components/ui/input-group';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import erp from '@/routes/erp';
import { InventoryMovementType, inventoryMovementTypeLabel } from '@/types/erp/erp-enums';
import { Form, Link } from '@inertiajs/react';
import { HelpCircle } from 'lucide-react';
import { Activity, useState } from 'react';

export default function NewInventoryMovementForm() {
    const [productId, setProductId] = useState<number | null>(null);

    return (
        <Form
            {...erp.inventory.store.form()}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
            transform={(data) => ({
                ...data,
                product_id: productId,
            })}
        >
            {({ processing, resetAndClearErrors, errors }) => (
                <>
                    <Field aria-invalid={!!errors.product_id}>
                        <FieldLabel htmlFor="product_id">Product</FieldLabel>
                        <NewInventoryInventorySelectCombobox value={productId} setValue={setProductId} />
                        <InputError message={errors.product_id} />
                    </Field>

                    <Field data-invalid={!!errors.movement_type}>
                        <FieldLabel htmlFor="movement_type">Movement Type</FieldLabel>
                        <Select name="movement_type" required aria-required>
                            <SelectTrigger className="w-full" aria-invalid={!!errors.movement_type}>
                                <SelectValue placeholder="Select a type" />
                            </SelectTrigger>
                            <SelectContent>
                                {Object.values(InventoryMovementType).map((type) => (
                                    <SelectItem key={type} value={type}>
                                        {inventoryMovementTypeLabel(type)}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </Field>

                    <Field aria-invalid={!!errors.quantity}>
                        <FieldLabel htmlFor="quantity">Quantity</FieldLabel>
                        <InputGroup>
                            <InputGroupInput
                                type="number"
                                id="quantity"
                                name="quantity"
                                defaultValue={0}
                                min={1}
                                step={1}
                                required
                                aria-required
                                aria-invalid={!!errors.quantity}
                            />

                            <InputGroupAddon align="inline-end">
                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <InputGroupButton variant="ghost" aria-label="Help" size="icon-xs">
                                            <HelpCircle aria-hidden />
                                        </InputGroupButton>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>The quantity to adjust from the product&apos;s current stock.</p>
                                    </TooltipContent>
                                </Tooltip>
                            </InputGroupAddon>
                        </InputGroup>

                        <InputError message={errors.quantity} />
                    </Field>

                    <Field aria-invalid={!!errors.reason}>
                        <FieldLabel htmlFor="reason">
                            Reason <span className="text-xs text-muted-foreground">(optional)</span>
                        </FieldLabel>

                        <InputGroup>
                            <InputGroupInput
                                type="text"
                                id="reason"
                                name="reason"
                                maxLength={100}
                                aria-invalid={!!errors.reason}
                            />

                            <InputGroupAddon align="inline-end">
                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <InputGroupButton variant="ghost" aria-label="Help" size="icon-xs">
                                            <HelpCircle aria-hidden />
                                        </InputGroupButton>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>A brief explanation for the inventory adjustments being made.</p>
                                    </TooltipContent>
                                </Tooltip>
                            </InputGroupAddon>
                        </InputGroup>

                        <InputError message={errors.reason} />
                    </Field>

                    <Field aria-invalid={!!errors.reference}>
                        <FieldLabel htmlFor="reference">
                            Reference <span className="text-xs text-muted-foreground">(optional)</span>
                        </FieldLabel>

                        <InputGroup>
                            <InputGroupInput
                                type="text"
                                id="reference"
                                name="reference"
                                maxLength={100}
                                aria-invalid={!!errors.reference}
                            />

                            <InputGroupAddon align="inline-end">
                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <InputGroupButton variant="ghost" aria-label="Help" size="icon-xs">
                                            <HelpCircle aria-hidden />
                                        </InputGroupButton>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>
                                            Reference identifier such as a document number or transaction ID related to
                                            this adjustment.
                                        </p>
                                    </TooltipContent>
                                </Tooltip>
                            </InputGroupAddon>
                        </InputGroup>

                        <InputError message={errors.reference} />
                    </Field>

                    <p className="text-sm text-muted-foreground">
                        Please, review the information provided before submitting the form to ensure accuracy.
                    </p>

                    <ButtonGroup>
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.inventory.index()} prefetch="hover" as="button">
                                Cancel
                            </Link>
                        </Button>
                        <ButtonGroup>
                            <Button type="submit" disabled={processing}>
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner />
                                </Activity>
                                Submit
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => {
                                    setProductId(null);
                                    resetAndClearErrors();
                                }}
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
