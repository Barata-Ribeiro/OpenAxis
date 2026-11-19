import {
  Button,
  composeRenderProps,
  Header,
  ListBox,
  ListBoxItem,
  ListBoxSection,
  Popover,
  Select as SelectPrimitive,
  SelectValue as SelectValuePrimitive,
  Separator,
  type ListBoxSectionProps,
  type SelectProps,
  type SelectValueProps,
} from "react-aria-components"
import { CheckIcon, ChevronDownIcon } from "lucide-react"
import * as React from "react"

import { cn } from "@/lib/utils"

function Select<T extends object, M extends "single" | "multiple" = "single">({
  ...props
}: SelectProps<T, M>) {
  return <SelectPrimitive data-slot="select" {...props} />
}

function SelectGroup<T extends object>({
  ...props
}: ListBoxSectionProps<T>) {
  return <ListBoxSection data-slot="select-group" {...props} />
}

function SelectValue<T extends object>({
  ...props
}: SelectValueProps<T>) {
  return <SelectValuePrimitive data-slot="select-value" {...props} />
}

function SelectTrigger({
  className,
  size = "default",
  children,
  ...props
}: Omit<React.ComponentProps<typeof Button>, "children" | "className"> & {
  children?: React.ReactNode
  className?: string
  size?: "sm" | "default"
}) {
  return (
    <Button
      data-slot="select-trigger"
      data-size={size}
      className={cn(
        "border-input data-[placeholder]:text-muted-foreground [&_svg:not([class*='text-'])]:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:bg-input/30 dark:hover:bg-input/50 flex w-fit items-center justify-between gap-2 rounded-md border bg-transparent px-3 py-2 text-sm whitespace-nowrap shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 data-[size=default]:h-9 data-[size=sm]:h-8 *:data-[slot=select-value]:line-clamp-1 *:data-[slot=select-value]:flex *:data-[slot=select-value]:items-center *:data-[slot=select-value]:gap-2 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
        className
      )}
      {...props}
    >
      {children}
      <ChevronDownIcon className="opacity-50" aria-hidden="true" />
    </Button>
  )
}

function SelectContent({
  className,
  children,
  placement = "bottom",
  offset = 4,
  ...props
}: Omit<React.ComponentProps<typeof Popover>, "className" | "children"> & {
  className?: string
  children?: React.ReactNode
}) {
  return (
    <Popover
      data-slot="select-content"
      placement={placement}
      offset={offset}
      className={cn(
        "bg-popover text-popover-foreground data-entering:animate-in data-exiting:animate-out data-exiting:fade-out-0 data-entering:fade-in-0 data-exiting:zoom-out-95 data-entering:zoom-in-95 data-[placement=bottom]:slide-in-from-top-2 data-[placement=left]:slide-in-from-right-2 data-[placement=right]:slide-in-from-left-2 data-[placement=top]:slide-in-from-bottom-2 relative z-50 w-(--trigger-width) min-w-[8rem] origin-(--trigger-anchor-point) overflow-x-hidden overflow-y-auto rounded-md border shadow-md",
        className
      )}
      {...props}
    >
      <ListBox className="max-h-[inherit] overflow-y-auto p-1 outline-hidden">
        {children}
      </ListBox>
    </Popover>
  )
}

function SelectLabel({
  className,
  ...props
}: React.ComponentProps<typeof Header>) {
  return (
    <Header
      data-slot="select-label"
      className={cn("text-muted-foreground px-2 py-1.5 text-xs", className)}
      {...props}
    />
  )
}

function SelectItem({
  className,
  children,
  ...props
}: React.ComponentProps<typeof ListBoxItem>) {
  return (
    <ListBoxItem
      data-slot="select-item"
      textValue={typeof children === "string" ? children : undefined}
      className={composeRenderProps(className, (className) => cn(
        "data-focused:bg-accent data-focused:text-accent-foreground [&_svg:not([class*='text-'])]:text-muted-foreground relative flex w-full cursor-default items-center gap-2 rounded-sm py-1.5 pr-8 pl-2 text-sm outline-hidden select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
        className
      ))}
      {...props}
    >
      {composeRenderProps(children, (children, { isSelected }) => (
        <>
          <span className="flex items-center gap-2">{children}</span>
          {isSelected && (
            <span
              data-slot="select-item-indicator"
              className="absolute right-2 flex size-3.5 items-center justify-center"
              aria-hidden="true"
            >
              <CheckIcon />
            </span>
          )}
        </>
      ))}
    </ListBoxItem>
  )
}

function SelectSeparator({
  className,
  ...props
}: React.ComponentProps<typeof Separator>) {
  return (
    <Separator
      data-slot="select-separator"
      className={cn("bg-border pointer-events-none -mx-1 my-1 h-px", className)}
      {...props}
    />
  )
}

export {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectSeparator,
  SelectTrigger,
  SelectValue,
}
