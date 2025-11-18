import { CircleCheckIcon, InfoIcon, Loader2Icon, OctagonXIcon, TriangleAlertIcon } from 'lucide-react';
import { useTheme } from 'next-themes';
import { Toaster as Sonner, type ToasterProps } from 'sonner';

const Toaster = ({ ...props }: ToasterProps) => {
    const { theme = 'system' } = useTheme();

    return (
        <Sonner
            theme={theme as ToasterProps['theme']}
            className="toaster group"
            icons={{
                success: <CircleCheckIcon aria-hidden size={16} />,
                info: <InfoIcon aria-hidden size={16} />,
                warning: <TriangleAlertIcon aria-hidden size={16} />,
                error: <OctagonXIcon aria-hidden size={16} />,
                loading: <Loader2Icon aria-hidden size={16} className="animate-spin" />,
            }}
            style={
                {
                    '--normal-bg': 'var(--popover)',
                    '--normal-text': 'var(--popover-foreground)',
                    '--normal-border': 'var(--border)',
                    '--border-radius': 'var(--radius)',
                } as React.CSSProperties
            }
            richColors
            closeButton
            {...props}
        />
    );
};

export { Toaster };
