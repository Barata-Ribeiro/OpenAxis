import { Separator as SeparatorPrimitive } from "react-aria-components"
import * as React from "react"

import { cn } from "@/lib/utils"

function Separator({
  className,
  orientation = "horizontal",
  decorative = true,
  ...props
}: React.ComponentProps<typeof SeparatorPrimitive> & { decorative?: boolean }) {
  return (
    <SeparatorPrimitive
      data-slot="separator-root"
      aria-hidden={decorative || undefined}
      orientation={orientation}
      className={cn(
        "bg-border block shrink-0 border-0 [:is(hr)]:h-px [:is(hr)]:w-full aria-[orientation=horizontal]:h-px aria-[orientation=horizontal]:w-full aria-[orientation=vertical]:h-full aria-[orientation=vertical]:w-px",
        className
      )}
      {...props}
    />
  )
}

export { Separator }
