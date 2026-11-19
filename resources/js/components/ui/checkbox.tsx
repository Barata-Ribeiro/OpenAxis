import { cn } from "@/lib/utils"
import {
  Checkbox as CheckboxPrimitive,
  composeRenderProps,
  type CheckboxProps,
} from "react-aria-components"

import { CheckIcon, MinusIcon } from "lucide-react"

function Checkbox({ className, children, ...props }: CheckboxProps) {
  return (
    <CheckboxPrimitive
      data-slot="checkbox"
      className={composeRenderProps(className, (className) => cn(
        "group/checkbox inline-flex items-center gap-3 text-sm leading-none font-medium select-none data-disabled:cursor-not-allowed data-disabled:opacity-50",
        className
      ))}
      {...props}
    >
      {composeRenderProps(
        children,
        (children, { isSelected, isIndeterminate }) => (
          <>
            <span
              data-slot="checkbox-indicator"
              aria-hidden="true"
              className="grid size-4 shrink-0 place-content-center rounded-[4px] border border-input shadow-xs transition-shadow group-data-focus-visible/checkbox:border-ring group-data-focus-visible/checkbox:ring-[3px] group-data-focus-visible/checkbox:ring-ring/50 group-data-invalid/checkbox:border-destructive group-data-selected/checkbox:border-primary group-data-selected/checkbox:bg-primary group-data-selected/checkbox:text-primary-foreground group-data-indeterminate/checkbox:border-primary group-data-indeterminate/checkbox:bg-primary group-data-indeterminate/checkbox:text-primary-foreground [&>svg]:size-3.5"
            >
              {isIndeterminate ? <MinusIcon /> : isSelected && <CheckIcon />}
            </span>
            {children}
          </>
        )
      )}
    </CheckboxPrimitive>
  )
}

export { Checkbox }
