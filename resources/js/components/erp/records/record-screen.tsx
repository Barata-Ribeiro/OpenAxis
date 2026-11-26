import Modal from '@/components/custom-ui/modal';
import { Card, CardContent } from '@/components/ui/card';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import type { ReactNode } from 'react';

export type RecordScreenProps = { title: string; modal?: boolean };

export function RecordScreen({
    title,
    modal,
    description,
    children,
}: Readonly<
    RecordScreenProps & {
        description: string;
        children: (state: { close?: () => void; setProcessing: (processing: boolean) => void }) => ReactNode;
    }
>) {
    const [processing, setProcessing] = useState(false);

    if (modal) {
        return (
            <Modal title={title} description={description} processing={processing}>
                {(close) => children({ close, setProcessing })}
            </Modal>
        );
    }

    return (
        <section className="flex min-w-0 flex-col gap-5 p-4 sm:p-6">
            <Head title={title} />
            <h1 className="text-2xl font-semibold tracking-tight">{title}</h1>
            <Card className="w-full max-w-3xl">
                <CardContent>{children({ setProcessing })}</CardContent>
            </Card>
        </section>
    );
}
