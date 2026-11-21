import { Check, ChevronsUpDown } from 'lucide-react';
import * as React from 'react';
import { composeRenderProps } from 'react-aria-components';

import { Badge } from '@/components/ui/badge';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
    CommandSeparator,
} from '@/components/ui/command';
import { Popover, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';

const FACETED_NAME = 'Faceted';
const TRIGGER_NAME = 'FacetedTrigger';
const BADGE_LIST_NAME = 'FacetedBadgeList';
const CONTENT_NAME = 'FacetedContent';
const LIST_NAME = 'FacetedList';
const ITEM_NAME = 'FacetedItem';

const ERRORS = {
    [TRIGGER_NAME]: `\`${TRIGGER_NAME}\` must be within \`${FACETED_NAME}\``,
    [BADGE_LIST_NAME]: `\`${BADGE_LIST_NAME}\` must be within \`${FACETED_NAME}\``,
    [CONTENT_NAME]: `\`${CONTENT_NAME}\` must be within \`${FACETED_NAME}\``,
    [LIST_NAME]: `\`${LIST_NAME}\` must be within \`${FACETED_NAME}\``,
    [ITEM_NAME]: `\`${ITEM_NAME}\` must be within \`${FACETED_NAME}\``,
};

type FacetedValue<Multiple extends boolean> = Multiple extends true ? Array<string> : string;
type FacetedSelection = 'all' | Set<React.Key>;

interface FacetedContextValue {
    value?: string | Array<string>;
    onSelectionChange: (selection: FacetedSelection) => void;
    isOpen: boolean;
    onOpenChange: (isOpen: boolean) => void;
    multiple: boolean;
}

const FacetedContext = React.createContext<FacetedContextValue | null>(null);

function useFacetedContext(name: keyof typeof ERRORS) {
    const context = React.useContext(FacetedContext);
    if (!context) {
        throw new Error(ERRORS[name]);
    }
    return context;
}

interface FacetedProps<Multiple extends boolean = false> {
    value?: FacetedValue<Multiple>;
    onValueChange?: (value: FacetedValue<Multiple> | undefined) => void;
    children?: React.ReactNode;
    multiple?: Multiple;
    isOpen?: boolean;
    defaultOpen?: boolean;
    onOpenChange?: (isOpen: boolean) => void;
}

function Faceted<Multiple extends boolean = false>(props: Readonly<FacetedProps<Multiple>>) {
    const {
        value,
        onValueChange,
        children,
        multiple = false as Multiple,
        isOpen: controlledOpen,
        defaultOpen = false,
        onOpenChange: onOpenChangeProp,
    } = props;
    const [uncontrolledOpen, setUncontrolledOpen] = React.useState(defaultOpen);
    const isOpen = controlledOpen ?? uncontrolledOpen;
    const onOpenChange = React.useCallback(
        (nextOpen: boolean) => {
            if (controlledOpen === undefined) {
                setUncontrolledOpen(nextOpen);
            }
            onOpenChangeProp?.(nextOpen);
        },
        [controlledOpen, onOpenChangeProp],
    );
    const onSelectionChange = React.useCallback(
        (selection: FacetedSelection) => {
            if (selection === 'all') return;

            const selectedValues = Array.from(selection, String);

            if (multiple) {
                onValueChange?.(selectedValues as FacetedValue<Multiple>);
            } else {
                onValueChange?.(selectedValues[0] as FacetedValue<Multiple> | undefined);
                onOpenChange(false);
            }
        },
        [multiple, onOpenChange, onValueChange],
    );
    const contextValue = React.useMemo<FacetedContextValue>(
        () => ({ value, onSelectionChange, isOpen, onOpenChange, multiple }),
        [value, onSelectionChange, isOpen, onOpenChange, multiple],
    );

    return <FacetedContext.Provider value={contextValue}>{children}</FacetedContext.Provider>;
}

type FacetedTriggerProps = Omit<React.ComponentProps<typeof PopoverTrigger>, 'isOpen' | 'defaultOpen' | 'onOpenChange'>;

function FacetedTrigger({ children, ...triggerProps }: Readonly<FacetedTriggerProps>) {
    const context = useFacetedContext(TRIGGER_NAME);

    return (
        <PopoverTrigger
            data-slot="faceted-trigger"
            {...triggerProps}
            isOpen={context.isOpen}
            onOpenChange={context.onOpenChange}
        >
            {children}
        </PopoverTrigger>
    );
}

interface FacetedBadgeListProps extends React.ComponentProps<'div'> {
    options?: Array<{ label: string; value: string }>;
    max?: number;
    badgeClassName?: string;
    placeholder?: string;
}

function FacetedBadgeList({
    ref,
    options = [],
    max = 2,
    placeholder = 'Select options...',
    className,
    badgeClassName,
    ...badgeListProps
}: Readonly<FacetedBadgeListProps>) {
    const context = useFacetedContext(BADGE_LIST_NAME);
    const values: string[] =
        context.value === undefined ? [] : Array.isArray(context.value) ? context.value : [context.value];

    const getLabel = React.useCallback(
        (value: string) => {
            const option = options.find((opt) => opt.value === value);
            return option?.label ?? value;
        },
        [options],
    );

    if (values.length === 0) {
        return (
            <div
                data-slot="faceted-badge-list"
                {...badgeListProps}
                ref={ref}
                className={cn('flex w-full items-center gap-1 text-muted-foreground', className)}
            >
                {placeholder}
                <ChevronsUpDown className="ml-auto size-4 shrink-0 opacity-50" />
            </div>
        );
    }

    return (
        <div
            data-slot="faceted-badge-list"
            {...badgeListProps}
            ref={ref}
            className={cn('flex flex-wrap items-center gap-1', className)}
        >
            {values.length > max ? (
                <Badge variant="secondary" className={cn('rounded-sm px-1 font-normal', badgeClassName)}>
                    {values.length} selected
                </Badge>
            ) : (
                values.map((value) => (
                    <Badge
                        key={value}
                        variant="secondary"
                        className={cn('rounded-sm px-1 font-normal', badgeClassName)}
                    >
                        <span className="truncate">{getLabel(value)}</span>
                    </Badge>
                ))
            )}
        </div>
    );
}

function FacetedContent({
    className,
    children,
    placement = 'bottom start',
    ...contentProps
}: Omit<React.ComponentProps<typeof Popover>, 'children'> & { children?: React.ReactNode }) {
    useFacetedContext(CONTENT_NAME);

    return (
        <Popover
            data-slot="faceted-content"
            {...contentProps}
            placement={placement}
            className={cn('w-[200px] origin-(--trigger-anchor-point) p-0', className)}
        >
            <Command>{children}</Command>
        </Popover>
    );
}

const FacetedInput = CommandInput;
const FacetedEmpty = CommandEmpty;
const FacetedGroup = CommandGroup;
const FacetedSeparator = CommandSeparator;

function FacetedList<T extends object>({
    onSelectionChange,
    ...listProps
}: React.ComponentProps<typeof CommandList<T>>) {
    const context = useFacetedContext(LIST_NAME);
    const selectedKeys = new Set(
        context.value === undefined ? [] : Array.isArray(context.value) ? context.value : [context.value],
    );

    return (
        <CommandList
            {...listProps}
            selectionMode={context.multiple ? 'multiple' : 'single'}
            selectedKeys={selectedKeys}
            shouldCloseOnSelect={!context.multiple}
            onSelectionChange={(selection) => {
                context.onSelectionChange(selection);
                onSelectionChange?.(selection);
            }}
        />
    );
}

type FacetedItemProps = Omit<React.ComponentProps<typeof CommandItem>, 'id' | 'textValue' | 'value'> & {
    value: string;
    textValue?: string;
};

function FacetedItem({ className, children, value, textValue = value, ...itemProps }: FacetedItemProps) {
    useFacetedContext(ITEM_NAME);
    return (
        <CommandItem
            data-slot="faceted-item"
            id={value}
            textValue={textValue}
            className={cn('gap-2 [&>svg:last-child]:hidden', className)}
            {...itemProps}
        >
            {composeRenderProps(children, (itemChildren, { isSelected }) => (
                <>
                    <span
                        className={cn(
                            'flex size-4 items-center justify-center rounded-sm border border-primary',
                            isSelected ? 'bg-primary text-primary-foreground' : 'opacity-50',
                        )}
                    >
                        {isSelected && <Check className="size-4" />}
                    </span>
                    {itemChildren}
                </>
            ))}
        </CommandItem>
    );
}

export {
    Faceted,
    FacetedBadgeList,
    FacetedContent,
    FacetedEmpty,
    FacetedGroup,
    FacetedInput,
    FacetedItem,
    FacetedList,
    FacetedSeparator,
    FacetedTrigger,
};
