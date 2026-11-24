import { Dialog, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { ScrollArea } from '@/components/ui/scroll-area';
import { HeadlessModal } from '@inertiaui/modal-react';
import type { ReactNode } from 'react';

type Props = {
    title: string;
    description: string;
    processing: boolean;
    children: (close: () => void) => ReactNode;
};

type ModalState = {
    isOpen: boolean;
    close: () => void;
};

export default function Modal({ title, description, processing, children }: Readonly<Props>) {
    return (
        <HeadlessModal>
            {({ isOpen, close }: Readonly<ModalState>) => (
                <Dialog
                    isOpen={isOpen}
                    onOpenChange={(open) => {
                        if (!open && !processing) close();
                    }}
                    className="max-h-9/10 overflow-hidden"
                    showCloseButton={!processing}
                >
                    <DialogHeader className="shrink-0">
                        <DialogTitle>{title}</DialogTitle>
                        <DialogDescription>{description}</DialogDescription>
                    </DialogHeader>

                    <ScrollArea className="grid min-h-0">{children(close)}</ScrollArea>
                </Dialog>
            )}
        </HeadlessModal>
    );
}
