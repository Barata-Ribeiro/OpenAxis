import { composeRenderProps, ToggleButton as ToggleItemPrimitive, ToggleButtonGroup as ToggleGroupPrimitive } from "react-aria-components"
import { type VariantProps } from "class-variance-authority"
import * as React from "react"

import { toggleVariants } from "@/components/ui/toggle"
import { cn } from "@/lib/utils"

const ToggleGroupContext = React.createContext<
  VariantProps<typeof toggleVariants>
>({
  size: "default",
  variant: "default",
})

function ToggleGroup({
  className,
  variant,
  size,
  children,
  ...props
}: React.ComponentProps<typeof ToggleGroupPrimitive> &
  VariantProps<typeof toggleVariants>) {
  return (
    <ToggleGroupPrimitive
      data-slot="toggle-group"
      data-variant={variant}
      data-size={size}
      className={cn(
        "group/toggle-group flex items-center data-vertical:flex-col data-vertical:items-stretch rounded-md data-[variant=outline]:shadow-xs",
        className
      )}
      {...props}
    >
      {composeRenderProps(children, (children) => (
        <ToggleGroupContext.Provider value={{ variant, size }}>
          {children}
        </ToggleGroupContext.Provider>
      ))}
    </ToggleGroupPrimitive>
  )
}

function ToggleGroupItem({
  className,
  children,
  variant,
  size,
  ...props
}: React.ComponentProps<typeof ToggleItemPrimitive> &
  VariantProps<typeof toggleVariants>) {
  const context = React.useContext(ToggleGroupContext)

  return (
    <ToggleItemPrimitive
      data-slot="toggle-group-item"
      data-variant={context.variant || variant}
      data-size={context.size || size}
      className={composeRenderProps(className, (className) => cn(
        toggleVariants({
          variant: context.variant || variant,
          size: context.size || size,
        }),
        "min-w-0 shrink-0 rounded-none shadow-none group-data-horizontal/toggle-group:first:rounded-l-md group-data-horizontal/toggle-group:last:rounded-r-md group-data-vertical/toggle-group:first:rounded-t-md group-data-vertical/toggle-group:last:rounded-b-md focus:z-10 focus-visible:z-10 group-data-horizontal/toggle-group:data-[variant=outline]:border-l-0 group-data-horizontal/toggle-group:data-[variant=outline]:first:border-l group-data-vertical/toggle-group:data-[variant=outline]:border-t-0 group-data-vertical/toggle-group:data-[variant=outline]:first:border-t",
        className
      ))}
      {...props}
    >
      {children}
    </ToggleItemPrimitive>
  )
}

export { ToggleGroup, ToggleGroupItem }
