'use client';

import { TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';
import * as TooltipPrimitive from '@radix-ui/react-tooltip';
import { motion, type Transition } from 'motion/react';
import { Children, forwardRef, isValidElement, type ComponentProps, type ReactElement, type ReactNode } from 'react';

type TooltipContentProps = ComponentProps<typeof TooltipContent>;

type AvatarGroupTooltipProps = TooltipContentProps;

function AvatarGroupTooltip(props: AvatarGroupTooltipProps) {
    return <TooltipContent {...props} />;
}

interface AvatarMotionProps {
    children: ReactNode;
    zIndex: number;
    translate: string | number;
    transition: Transition;
    tooltipContent?: ReactNode;
    tooltipProps?: Partial<TooltipContentProps>;
}

function AvatarMotionContainer({
    children,
    zIndex,
    translate,
    transition,
    tooltipContent,
    tooltipProps,
}: AvatarMotionProps) {
    return (
        <TooltipPrimitive.Root>
            <TooltipTrigger asChild>
                <motion.div
                    data-slot="avatar-container"
                    className="relative"
                    style={{ zIndex }}
                    whileHover={{ y: translate }}
                    transition={transition}
                >
                    {children}
                </motion.div>
            </TooltipTrigger>

            {tooltipContent ? <AvatarGroupTooltip {...tooltipProps}>{tooltipContent}</AvatarGroupTooltip> : null}
        </TooltipPrimitive.Root>
    );
}

interface AvatarCSSProps {
    children: ReactNode;
    zIndex: number;
    tooltipContent?: ReactNode;
    tooltipProps?: Partial<TooltipContentProps>;
}

function AvatarCSSContainer({ children, zIndex, tooltipContent, tooltipProps }: AvatarCSSProps) {
    return (
        <TooltipPrimitive.Root>
            <TooltipTrigger asChild>
                <div
                    data-slot="avatar-container"
                    className="relative transition-transform duration-300 ease-out hover:-translate-y-2"
                    style={{ zIndex }}
                >
                    {children}
                </div>
            </TooltipTrigger>

            {tooltipContent ? <AvatarGroupTooltip {...tooltipProps}>{tooltipContent}</AvatarGroupTooltip> : null}
        </TooltipPrimitive.Root>
    );
}

interface AvatarStackItemProps {
    children: ReactNode;
    index: number;
    size: number;
    className?: string;
}

function AvatarStackItem({ children, index, size, className }: AvatarStackItemProps) {
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
                    : undefined,
            }}
        >
            {children}
        </div>
    );
}

type AvatarGroupVariant = 'motion' | 'css' | 'stack';

type AvatarGroupProps = Omit<ComponentProps<'div'>, 'translate' | 'children'> & {
    children: ReactNode;
    variant?: AvatarGroupVariant;
    transition?: Transition;
    invertOverlap?: boolean;
    translate?: string | number;
    tooltipProps?: Partial<TooltipContentProps>;
    animate?: boolean;
    size?: number;
};

const AvatarGroup = forwardRef<HTMLDivElement, AvatarGroupProps>(function AvatarGroup(
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
) {
    const elements = Children.toArray(children).filter((child): child is ReactElement => isValidElement(child));
    const count = elements.length;

    if (variant === 'stack') {
        return (
            <div
                ref={ref}
                className={cn('flex items-center -space-x-1', animate && '*:transition-all hover:space-x-0', className)}
                {...props}
            >
                {elements.map((child, index) => (
                    <AvatarStackItem key={child.key ?? index} index={index} size={size}>
                        {child}
                    </AvatarStackItem>
                ))}
            </div>
        );
    }

    return (
        <TooltipProvider delayDuration={0}>
            <div
                ref={ref}
                data-slot="avatar-group"
                className={cn(
                    'flex items-center',
                    variant === 'css' && '-space-x-3',
                    variant === 'motion' && 'h-8 flex-row -space-x-2',
                    className,
                )}
                {...props}
            >
                {elements.map((child, index) => {
                    const zIndex = invertOverlap ? count - index : index;

                    if (variant === 'motion') {
                        return (
                            <AvatarMotionContainer
                                key={child.key ?? index}
                                zIndex={zIndex}
                                translate={translate}
                                transition={transition}
                                tooltipProps={tooltipProps}
                            >
                                {child}
                            </AvatarMotionContainer>
                        );
                    }

                    return (
                        <AvatarCSSContainer key={child.key ?? index} zIndex={zIndex} tooltipProps={tooltipProps}>
                            {child}
                        </AvatarCSSContainer>
                    );
                })}
            </div>
        </TooltipProvider>
    );
});

export {
    AvatarGroup,
    AvatarGroupTooltip,
    type AvatarGroupProps,
    type AvatarGroupTooltipProps,
    type AvatarGroupVariant,
};
