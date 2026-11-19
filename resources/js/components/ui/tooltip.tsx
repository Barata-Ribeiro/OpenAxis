import * as React from "react"
import { cn } from "@/lib/utils"
import {
  OverlayArrow,
  Tooltip as TooltipPrimitive,
  TooltipTrigger as TooltipTriggerPrimitive,
} from "react-aria-components"

function TooltipTrigger({
  delay = 0,
  ...props
}: React.ComponentProps<typeof TooltipTriggerPrimitive>) {
  return <TooltipTriggerPrimitive delay={delay} {...props} />
}

function Tooltip({
  className,
  placement = "top",
  offset = 4,
  crossOffset = 0,
  children,
  ...props
}: Omit<
  React.ComponentProps<typeof TooltipPrimitive>,
  "children" | "className"
> & {
  className?: string
  children?: React.ReactNode
}) {
  return (
    <TooltipPrimitive
      data-slot="tooltip-content"
      placement={placement}
      offset={offset}
      crossOffset={crossOffset}
      className={cn(
        "bg-primary text-primary-foreground data-entering:animate-in data-entering:fade-in-0 data-entering:zoom-in-95 data-exiting:animate-out data-exiting:fade-out-0 data-exiting:zoom-out-95 data-[placement=bottom]:slide-in-from-top-2 data-[placement=left]:slide-in-from-right-2 data-[placement=right]:slide-in-from-left-2 data-[placement=top]:slide-in-from-bottom-2 z-50 max-w-sm origin-(--trigger-anchor-point) rounded-md px-3 py-1.5 text-xs",
        className
      )}
      {...props}
    >
      {children}
      <OverlayArrow
        className="z-50 size-2.5 translate-y-[calc(-50%-2px)] rotate-45 rounded-xs bg-primary fill-primary"
        style={({ placement, defaultStyle }) => ({
          ...defaultStyle,
          rotate: "0deg",
          translate: "0 0",
          transform:
            placement === "bottom"
              ? "translate(-50%, calc(50% + 2px)) rotate(45deg)"
              : placement === "top"
                ? "translate(-50%, calc(-50% - 2px)) rotate(45deg)"
                : placement === "left"
                  ? "translate(calc(-50% - 2px), -50%) rotate(45deg)"
                  : "translate(calc(50% + 2px), -50%) rotate(45deg)",
        })}
      />
    </TooltipPrimitive>
  )
}

export { Tooltip, TooltipTrigger }
