import { FieldDescription, FieldGroup, FieldLegend, FieldSet } from '@/components/ui/field';
import erp from '@/routes/erp';
import { User } from '@/types/application/user';
import { Deferred, Form, Link } from '@inertiajs/react';
import { Activity, useState } from 'react';

import InputError from '@/components/feedback/input-error';
import InputSkeleton from '@/components/feedback/skeletons/input-skeleton';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Calendar } from '@/components/ui/calendar';
import { Checkbox } from '@/components/ui/checkbox';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Field, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Spinner } from '@/components/ui/spinner';
import { Check, ChevronDownIcon, ChevronsUpDown } from 'lucide-react';

export default function NewVendorForm({ users }: { users: Array<User> }) {
    // State for Date of Birth picker
    const [dateOfBirth, setDateOfBirth] = useState<Date | undefined>(undefined);
    const [bodOpen, setBodOpen] = useState(false);

    // State for user selection
    const [open, setOpen] = useState(false);
    const [value, setValue] = useState<number | null>(null);

    return (
        <Form
            {...erp.vendors.store.form()}
            options={{ preserveScroll: true }}
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
            disableWhileProcessing
            transform={(data) => ({
                ...data,
                date_of_birth: dateOfBirth?.toISOString().split('T')[0] ?? null,
                user_id: value,
            })}
        >
            {({ processing, resetAndClearErrors, errors }) => (
                <>
                    <FieldSet>
                        <FieldLegend>Personal Information</FieldLegend>
                        <FieldDescription>
                            Provide the vendor's personal details to create their profile.
                        </FieldDescription>

                        <FieldGroup className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Field aria-invalid={!!errors.first_name}>
                                <FieldLabel htmlFor="first_name">First Name</FieldLabel>
                                <Input
                                    type="text"
                                    id="first_name"
                                    name="first_name"
                                    placeholder="e.g. John"
                                    required
                                    aria-required
                                    aria-invalid={!!errors.first_name}
                                />
                                <InputError message={errors.first_name} />
                            </Field>
                            <Field aria-invalid={!!errors.last_name}>
                                <FieldLabel htmlFor="last_name">Last Name</FieldLabel>
                                <Input
                                    type="text"
                                    id="last_name"
                                    name="last_name"
                                    placeholder="e.g. Doe"
                                    required
                                    aria-required
                                    aria-invalid={!!errors.last_name}
                                />
                                <InputError message={errors.last_name} />
                            </Field>
                        </FieldGroup>
                        <FieldGroup className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Field data-invalid={!!errors.date_of_birth}>
                                <FieldLabel htmlFor="date_of_birth">Date of Birth</FieldLabel>
                                <Popover open={bodOpen} onOpenChange={setBodOpen}>
                                    <PopoverTrigger asChild>
                                        <Button
                                            variant="outline"
                                            id="date"
                                            className="w-48 justify-between font-normal"
                                        >
                                            {dateOfBirth ? dateOfBirth.toLocaleDateString() : 'Select date'}
                                            <ChevronDownIcon />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent className="w-auto overflow-hidden p-0" align="start">
                                        <Calendar
                                            mode="single"
                                            selected={dateOfBirth ?? undefined}
                                            captionLayout="dropdown"
                                            showOutsideDays={false}
                                            disabled={(date) => date < new Date('1900-01-01')}
                                            onSelect={(date) => {
                                                setDateOfBirth(date);
                                                setBodOpen(false);
                                            }}
                                        />
                                    </PopoverContent>
                                </Popover>
                                <InputError message={errors.date_of_birth} />
                            </Field>
                            <Field data-invalid={!!errors.phone_number}>
                                <FieldLabel htmlFor="phone_number">Phone Number</FieldLabel>
                                <Input
                                    type="tel"
                                    id="phone_number"
                                    name="phone_number"
                                    placeholder="e.g. +1234567890"
                                    required
                                    aria-required
                                    aria-invalid={!!errors.phone_number}
                                />
                                <InputError message={errors.phone_number} />
                            </Field>
                        </FieldGroup>
                    </FieldSet>

                    <FieldSet>
                        <FieldLegend>Platform Information</FieldLegend>
                        <FieldDescription>
                            Detail the vendor&apos;s platform account, commission rates, and activation status.
                        </FieldDescription>

                        <Deferred data="users" fallback={<InputSkeleton />}>
                            <Field data-invalid={!!errors.user_id}>
                                <FieldLabel htmlFor="user_id">Associated User Account</FieldLabel>

                                <Popover open={open} onOpenChange={setOpen}>
                                    <PopoverTrigger asChild>
                                        <Button
                                            variant="outline"
                                            role="combobox"
                                            aria-expanded={open}
                                            className="justify-between"
                                        >
                                            {value ? users.find((user) => user.id === value)?.name : 'Select user...'}
                                            <ChevronsUpDown aria-hidden className="opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent className="p-0">
                                        <Command>
                                            <CommandInput placeholder="Search user..." className="h-9" />
                                            <CommandList>
                                                <CommandEmpty>No user found.</CommandEmpty>
                                                <CommandGroup>
                                                    {users
                                                        ? users.map((user) => {
                                                              const visibilityState =
                                                                  processing && value === user.id
                                                                      ? 'visible'
                                                                      : 'hidden';
                                                              return (
                                                                  <CommandItem
                                                                      key={user.id}
                                                                      value={user.name}
                                                                      onSelect={(currentValue) => {
                                                                          const selectedUser =
                                                                              users.find(
                                                                                  (u) => u.name === currentValue,
                                                                              ) ?? null;
                                                                          const selectedUserId = selectedUser
                                                                              ? selectedUser.id
                                                                              : null;
                                                                          setValue(selectedUserId);
                                                                          setOpen(false);
                                                                      }}
                                                                  >
                                                                      {user.name}
                                                                      <Activity mode={visibilityState}>
                                                                          <Check aria-hidden className="ml-auto" />
                                                                      </Activity>
                                                                  </CommandItem>
                                                              );
                                                          })
                                                        : null}
                                                </CommandGroup>
                                            </CommandList>
                                        </Command>
                                    </PopoverContent>
                                </Popover>
                            </Field>
                        </Deferred>

                        <Field data-invalid={!!errors.commission_rate}>
                            <FieldLabel htmlFor="commission_rate">Commission Rate (%)</FieldLabel>

                            <Input
                                type="number"
                                id="commission_rate"
                                name="commission_rate"
                                placeholder="e.g. 15"
                                min={0}
                                max={100}
                                step={0.01}
                                required
                                aria-required
                                aria-invalid={!!errors.commission_rate}
                            />

                            <InputError message={errors.commission_rate} />
                        </Field>

                        <Field data-invalid={!!errors.is_active}>
                            <div className="flex items-center space-x-3">
                                <Checkbox id="is_active" name="is_active" aria-invalid={!!errors.is_active} />
                                <FieldLabel htmlFor="is_active">Set as active</FieldLabel>
                            </div>
                            <InputError message={errors.is_active} />
                        </Field>
                    </FieldSet>

                    <p className="text-sm text-muted-foreground">
                        Please, review the information provided before submitting the form to ensure accuracy.
                    </p>

                    <ButtonGroup>
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={erp.vendors.index()} prefetch="hover" as="button">
                                Cancel
                            </Link>
                        </Button>
                        <ButtonGroup>
                            <Button type="submit" disabled={processing}>
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner />
                                </Activity>
                                Register Vendor
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => {
                                    resetAndClearErrors();
                                    setDateOfBirth(undefined);
                                    setValue(null);
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
