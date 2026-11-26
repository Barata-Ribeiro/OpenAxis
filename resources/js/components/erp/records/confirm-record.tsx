import InputError from '@/components/common/input-error';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import type { FormTarget } from '@/types/erp/forms';
import { Form } from '@inertiajs/react';

import { RecordScreen } from './record-screen';
import type { RecordScreenProps } from './record-screen';

export type ConfirmRecordProps = RecordScreenProps & { id: number };

export function ConfirmRecord({
    target,
    prompt,
    confirmLabel,
    ...props
}: Readonly<RecordScreenProps & { target: FormTarget; prompt: string; confirmLabel: string }>) {
    return (
        <RecordScreen {...props} description={prompt}>
            {({ close, setProcessing }) => (
                <Form
                    {...target}
                    className="space-y-5"
                    disableWhileProcessing
                    options={{ preserveScroll: true }}
                    onStart={() => setProcessing(true)}
                    onFinish={() => setProcessing(false)}
                    onSuccess={close}
                >
                    {({ errors, processing }) => (
                        <>
                            {!props.modal && <p className="text-sm text-muted-foreground">{prompt}</p>}
                            <InputError role="alert" message={errors['record']} />
                            <div className="flex flex-wrap gap-2">
                                {close && (
                                    <Button type="button" variant="outline" autoFocus onPress={close}>
                                        Back
                                    </Button>
                                )}
                                <Button type="submit" variant="destructive" isDisabled={processing}>
                                    {processing && <Spinner />}
                                    {confirmLabel}
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            )}
        </RecordScreen>
    );
}
