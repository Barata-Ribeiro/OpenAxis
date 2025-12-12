import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import type { RouteDefinition } from '@/wayfinder';
import type { Method } from '@inertiajs/core';
import { Link } from '@inertiajs/react';
import { type Dispatch, type SetStateAction } from 'react';

interface ActionConfirmationDialogProps {
    title: string;
    description: string;
    open: boolean;
    setOpen: Dispatch<SetStateAction<boolean>>;
    method: Method;
    route: RouteDefinition<Method>;
}

export default function ActionConfirmationDialog({
    title,
    description,
    open,
    setOpen,
    method,
    route,
}: Readonly<ActionConfirmationDialogProps>) {
    return (
        <AlertDialog open={open} onOpenChange={setOpen}>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>{title}</AlertDialogTitle>
                    <AlertDialogDescription>{description}</AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel onClick={() => setOpen(false)}>Cancel</AlertDialogCancel>
                    <AlertDialogAction asChild>
                        <Link href={route} method={method} as="button">
                            Confirm
                        </Link>
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
