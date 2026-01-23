import InputError from '@/components/feedback/input-error';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Checkbox } from '@/components/ui/checkbox';
import { Field, FieldGroup, FieldLabel, FieldLegend, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { InputGroup, InputGroupAddon, InputGroupText, InputGroupTextarea } from '@/components/ui/input-group';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';
import administrative from '@/routes/administrative';
import type { Role } from '@/types/application/role-permission';
import { Form, Link } from '@inertiajs/react';
import { InfoIcon } from 'lucide-react';
import { Activity, useMemo, useState } from 'react';

interface NotifierFormProps {
    roles: {
        value: Role['name'];
        label: string;
    }[];
}

export default function NotifierForm({ roles }: Readonly<NotifierFormProps>) {
    const [email, setEmail] = useState('');
    const [selectedRoles, setSelectedRoles] = useState<Record<string, boolean>>({});
    const [message, setMessage] = useState('');

    const anyRoleSelected = useMemo(() => Object.values(selectedRoles).some(Boolean), [selectedRoles]);
    const emailTyped = email.trim().length > 0;

    const handleCheckboxChange = (roleValue: string, checked: boolean) => {
        setSelectedRoles((prev) => {
            const next = { ...prev, [roleValue]: checked };
            if (Object.values(next).some(Boolean)) setEmail('');
            if (!Object.values(next).some(Boolean)) return {};
            return next;
        });
    };

    const remaining = Math.max(0, 120 - message.length);

    return (
        <Form
            {...administrative.notifier.notify.form()}
            options={{ preserveScroll: true }}
            disableWhileProcessing
            className="space-y-6 inert:pointer-events-none inert:opacity-60 inert:grayscale-100"
        >
            {({ processing, resetAndClearErrors, errors }) => (
                <>
                    <Field data-invalid={!!errors.message}>
                        <FieldLabel htmlFor="message">Message</FieldLabel>
                        <InputGroup>
                            <InputGroupTextarea
                                id="message"
                                name="message"
                                placeholder="Type the notification message"
                                maxLength={120}
                                required
                                aria-required
                                aria-invalid={!!errors.message}
                                value={message}
                                onChange={(e) => setMessage(e.target.value)}
                            />
                            <InputGroupAddon align="block-end">
                                <InputGroupText className="text-xs text-muted-foreground">
                                    {remaining} character{remaining !== 1 ? 's' : ''} left
                                </InputGroupText>
                            </InputGroupAddon>
                        </InputGroup>

                        <InputError message={errors.message} />
                    </Field>

                    <Field data-invalid={!!errors.email}>
                        <FieldLabel htmlFor="email">Specific email (optional)</FieldLabel>

                        <Input
                            id="email"
                            name="email"
                            type="email"
                            placeholder="user@example.com"
                            value={email}
                            onChange={(e) => {
                                const v = e.target.value;
                                setEmail(v);
                                if (v.trim().length > 0) setSelectedRoles({});
                            }}
                            aria-invalid={!!errors.email}
                            disabled={anyRoleSelected}
                        />

                        <InputError message={errors.email} />
                    </Field>

                    <FieldSet>
                        <FieldLegend>Roles</FieldLegend>

                        <FieldGroup className="grid gap-2">
                            {roles.map((r) => (
                                <Field data-invalid={!!errors.roles} key={r.value} orientation="horizontal">
                                    <Checkbox
                                        id={`role-${r.value}`}
                                        name="roles[]"
                                        value={r.value}
                                        checked={!!selectedRoles[r.value]}
                                        onCheckedChange={(v) => handleCheckboxChange(r.value, !!v)}
                                        disabled={emailTyped}
                                    />
                                    <FieldLabel htmlFor={`role-${r.value}`}>{r.label}</FieldLabel>
                                </Field>
                            ))}
                        </FieldGroup>

                        <InputError message={errors.roles} />
                    </FieldSet>

                    <Alert
                        className="border-blue-300 bg-blue-50 text-blue-800 dark:border-blue-800 dark:bg-gray-800 dark:text-blue-400"
                        aria-live="polite"
                        aria-labelledby="alert-title"
                        aria-describedby="alert-description"
                        role="alert"
                    >
                        <InfoIcon aria-hidden />
                        <AlertTitle id="alert-title">Broadcast Alert!</AlertTitle>
                        <AlertDescription id="alert-description">
                            Notifications will be sent in real time to all users who match the specified criteria. If
                            the user is offline, the notification will be saved in their inbox.
                        </AlertDescription>
                    </Alert>

                    <ButtonGroup>
                        <Button type="button" variant="outline" disabled={processing} asChild>
                            <Link href={dashboard()} prefetch="hover" as="button">
                                Cancel
                            </Link>
                        </Button>
                        <ButtonGroup>
                            <Button type="submit" disabled={processing}>
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner />
                                </Activity>
                                Send Notification
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => {
                                    resetAndClearErrors();
                                    setEmail('');
                                    setSelectedRoles({});
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
