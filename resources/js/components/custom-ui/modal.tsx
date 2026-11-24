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
                    className="flex max-h-9/10 flex-col overflow-hidden *:data-[slot=dialog]:min-h-0 *:data-[slot=dialog]:flex-col sm:max-w-2xl"
                    showCloseButton={!processing}
                >
                    <DialogHeader className="shrink-0 pr-8">
                        <DialogTitle>{title}</DialogTitle>
                        <DialogDescription>{description}</DialogDescription>
                    </DialogHeader>

                    <ScrollArea
                        className="min-h-0 overflow-x-hidden overscroll-contain p-1"
                        role="region"
                        tabIndex={0}
                        aria-label={title}
                    >
                        {children(close)}
                    </ScrollArea>
                </Dialog>
            )}
        </HeadlessModal>
    );
}
