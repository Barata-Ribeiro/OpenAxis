import { TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';
import * as TooltipPrimitive from '@radix-ui/react-tooltip';
import { motion, type Transition } from 'motion/react';
import * as React from 'react';

// Define types based on components
type TooltipContentProps = React.ComponentProps<typeof TooltipContent>;

// Avatar Container for motion-based interactions
interface AvatarMotionProps {
    children: React.ReactNode;
    zIndex: number;
    translate: string | number;
    transition: Transition;
    tooltipContent?: React.ReactNode;
    tooltipProps?: Partial<TooltipContentProps>;
}

function AvatarMotionContainer({
    children,
    zIndex,
    translate,
    transition,
    tooltipContent,
    tooltipProps,
}: Readonly<AvatarMotionProps>) {
    return (
        <TooltipPrimitive.Root>
            <TooltipTrigger>
                <motion.div
                    className="relative"
                    data-slot="avatar-container"
                    style={{ zIndex }}
                    transition={transition}
                    whileHover={{
                        y: translate,
                    }}
                >
                    {children}
                </motion.div>
            </TooltipTrigger>
            {tooltipContent && <AvatarGroupTooltip {...tooltipProps}>{tooltipContent}</AvatarGroupTooltip>}
        </TooltipPrimitive.Root>
    );
}

// Avatar Container for CSS-based interactions
interface AvatarCSSProps {
    children: React.ReactNode;
    zIndex: number;
    tooltipContent?: React.ReactNode;
    tooltipProps?: Partial<TooltipContentProps>;
}

function AvatarCSSContainer({ children, zIndex, tooltipContent, tooltipProps }: Readonly<AvatarCSSProps>) {
    return (
        <TooltipPrimitive.Root>
            <TooltipTrigger>
                <div
                    className="relative transition-transform duration-300 ease-out hover:-translate-y-2"
                    data-slot="avatar-container"
                    style={{ zIndex }}
                >
                    {children}
                </div>
            </TooltipTrigger>
            {tooltipContent && <AvatarGroupTooltip {...tooltipProps}>{tooltipContent}</AvatarGroupTooltip>}
        </TooltipPrimitive.Root>
    );
}

// Avatar Container for stack variant with mask
interface AvatarStackItemProps {
    children: React.ReactNode;
    index: number;
    size: number;
    className?: string;
}

function AvatarStackItem({ children, index, size, className }: Readonly<AvatarStackItemProps>) {
    return (
        <div
            className={cn(
                'size-full shrink-0 overflow-hidden rounded-full',
                '**:data-[slot="avatar"]:size-full',
                className,
            )}
            style={{
                width: size,
                height: size,
                maskImage: index
                    ? `radial-gradient(circle ${size / 2}px at -${size / 4 + size / 10}px 50%, transparent 99%, white 100%)`
                    : '',
            }}
        >
            {children}
        </div>
    );
}

type AvatarGroupTooltipProps = TooltipContentProps;

function AvatarGroupTooltip(props: AvatarGroupTooltipProps) {
    return <TooltipContent {...props} />;
}

const avatarElementKeys = new WeakMap<React.ReactElement, string>();

function getAvatarElementKey(child: React.ReactElement): string {
    if (child.key != null) {
        return String(child.key);
    }

    const existingKey = avatarElementKeys.get(child);

    if (existingKey) {
        return existingKey;
    }

    const generatedKey = crypto.randomUUID();
    avatarElementKeys.set(child, generatedKey);

    return generatedKey;
}

type AvatarGroupVariant = 'motion' | 'css' | 'stack';

type AvatarGroupProps = Omit<React.ComponentProps<'div'>, 'translate'> & {
    /**
     * We accept any renderable content so callers can pass conditionals or
     * fragments. Normalization happens internally using React.Children.toArray.
     */
    children: React.ReactNode;
    variant?: AvatarGroupVariant;
    transition?: Transition;
    invertOverlap?: boolean;
    translate?: string | number;
    tooltipProps?: Partial<TooltipContentProps>;
    // Stack-specific props
    animate?: boolean;
    size?: number;
};

const AvatarGroup = React.forwardRef<HTMLDivElement, AvatarGroupProps>(
    (
        {
            children,
            className,
            variant = 'motion',
            transition = { type: 'spring', stiffness: 300, damping: 17 },
            invertOverlap = false,
            translate = '-30%',
            tooltipProps = { side: 'top', sideOffset: 24 },
            animate = false,
            size = 40,
            ...props
        },
        ref,
    ) => {
        // Normalize children to an array of elements so we can safely map later.
        const normalizedChildren = React.Children.toArray(children).filter((c): c is React.ReactElement =>
            React.isValidElement(c),
        );

        // Stack variant
        if (variant === 'stack') {
            return (
                <div
                    className={cn(
                        'flex items-center -space-x-1',
                        animate && '*:transition-all hover:space-x-0',
                        className,
                    )}
                    ref={ref}
                    {...props}
                >
                    {normalizedChildren.map((child, index) => (
                        <AvatarStackItem
                            className={className}
                            index={index}
                            key={getAvatarElementKey(child)}
                            size={size}
                        >
                            {child}
                        </AvatarStackItem>
                    ))}
                </div>
            );
        }

        // Motion and CSS variants with tooltips
        return (
            <TooltipProvider delayDuration={0}>
                <div
                    className={cn(
                        'flex items-center',
                        variant === 'css' && '-space-x-3',
                        variant === 'motion' && 'h-8 flex-row -space-x-2',
                        className,
                    )}
                    data-slot="avatar-group"
                    ref={ref}
                    {...props}
                >
                    {normalizedChildren.map((child, index) => {
                        const zIndex = invertOverlap ? normalizedChildren.length - index : index;
                        const key = getAvatarElementKey(child);

                        if (variant === 'motion') {
                            return (
                                <AvatarMotionContainer
                                    key={key}
                                    tooltipProps={tooltipProps}
                                    transition={transition}
                                    translate={translate}
                                    zIndex={zIndex}
                                >
                                    {child}
                                </AvatarMotionContainer>
                            );
                        }

                        return (
                            <AvatarCSSContainer key={key} tooltipProps={tooltipProps} zIndex={zIndex}>
                                {child}
                            </AvatarCSSContainer>
                        );
                    })}
                </div>
            </TooltipProvider>
        );
    },
);

export {
    AvatarGroup,
    AvatarGroupTooltip,
    type AvatarGroupProps,
    type AvatarGroupTooltipProps,
    type AvatarGroupVariant,
};
