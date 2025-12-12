import { Button } from '@/components/ui/button';
import { useClipboard } from '@/hooks/use-clipboard';
import { ClipboardCheckIcon, ClipboardIcon } from 'lucide-react';
import { type ReactNode } from 'react';
import { toast } from 'sonner';

interface RawCopyButtonProps {
    content: unknown;
    children: ReactNode;
}

export default function DropdownMenuCopyButton({ content, children }: Readonly<RawCopyButtonProps>) {
    const [copiedText, copy] = useClipboard();

    const isContentInClipboard = copiedText === String(content);

    return (
        <Button
            type="button"
            variant="ghost"
            size="sm"
            className="w-full justify-start px-2"
            disabled={isContentInClipboard}
            onClick={() => copy(String(content)).then(() => toast.info('Copied to clipboard!', { duration: 2000 }))}
        >
            {isContentInClipboard ? (
                <ClipboardCheckIcon aria-hidden size={14} />
            ) : (
                <ClipboardIcon aria-hidden size={14} />
            )}
            {children}
        </Button>
    );
}
