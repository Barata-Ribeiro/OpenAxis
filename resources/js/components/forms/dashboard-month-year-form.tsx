import InputError from '@/components/feedback/input-error';
import { Field, FieldLabel } from '@/components/ui/field';
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from '@/components/ui/input-group';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';
import { Form, usePage } from '@inertiajs/react';
import { Activity } from 'react';

export default function DashboardMonthYearForm() {
    const { url } = usePage();

    const params = new URLSearchParams(url.split('?')[1]);
    const yearMonth = params.get('yearMonth') ?? '';

    return (
        <Form
            {...dashboard.form()}
            method="GET"
            className="w-full max-w-64 place-self-end inert:pointer-events-none inert:grayscale-100"
            disableWhileProcessing
        >
            {({ processing, errors }) => (
                <Field data-invalid={!!errors.yearMonth}>
                    <InputGroup>
                        <InputGroupInput id="yearMonth" name="yearMonth" type="month" defaultValue={yearMonth} />
                        <InputGroupAddon align="block-start">
                            <FieldLabel htmlFor="yearMonth">Select Month & Year</FieldLabel>
                            <InputGroupButton
                                type="submit"
                                className="ml-auto"
                                size="sm"
                                variant="default"
                                disabled={processing}
                            >
                                <Activity mode={processing ? 'visible' : 'hidden'}>
                                    <Spinner />
                                </Activity>
                                Filter
                            </InputGroupButton>
                        </InputGroupAddon>
                    </InputGroup>
                    <InputError message={errors.yearMonth} />
                </Field>
            )}
        </Form>
    );
}
