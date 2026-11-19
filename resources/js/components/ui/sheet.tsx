import {
  Button,
  Dialog as DialogPrimitive,
  DialogTrigger as DialogTriggerPrimitive,
  Heading,
  Modal,
  Text,
} from "react-aria-components"
import { XIcon } from "lucide-react"
import * as React from "react"

import { DialogOverlay } from "@/components/ui/dialog"
import { cn } from "@/lib/utils"

const SheetTrigger = DialogTriggerPrimitive

function SheetClose(props: React.ComponentProps<typeof Button>) {
  return <Button slot="close" data-slot="sheet-close" {...props} />
}

function Sheet({
  className,
  children,
  side = "right",
  showCloseButton = true,
  ...props
}: Omit<React.ComponentProps<typeof DialogOverlay>, "children"> & {
  children?: React.ReactNode
  showCloseButton?: boolean
  side?: "top" | "right" | "bottom" | "left"
}) {
  return (
    <DialogOverlay data-slot="sheet-overlay" {...props}>
      <Modal
        data-slot="sheet-content"
        className={cn(
          "bg-background data-entering:animate-in data-exiting:animate-out fixed flex flex-col gap-4 shadow-lg transition ease-in-out data-exiting:duration-300 data-entering:duration-500",
          side === "right" &&
            "data-exiting:slide-out-to-right data-entering:slide-in-from-right inset-y-0 right-0 h-full w-3/4 border-l sm:max-w-sm",
          side === "left" &&
            "data-exiting:slide-out-to-left data-entering:slide-in-from-left inset-y-0 left-0 h-full w-3/4 border-r sm:max-w-sm",
          side === "top" &&
            "data-exiting:slide-out-to-top data-entering:slide-in-from-top inset-x-0 top-0 h-auto border-b",
          side === "bottom" &&
            "data-exiting:slide-out-to-bottom data-entering:slide-in-from-bottom inset-x-0 bottom-0 h-auto border-t",
          className
        )}
      >
        <DialogPrimitive
          data-slot="sheet"
          className="flex h-full flex-col [align-items:inherit] [justify-content:inherit] [gap:inherit] outline-none"
        >
          {children}
          {showCloseButton && (
            <SheetClose
              className="ring-offset-background focus:ring-ring bg-secondary absolute top-4 right-4 rounded-xs opacity-70 transition-opacity hover:opacity-100 focus:ring-2 focus:ring-offset-2 focus:outline-hidden disabled:pointer-events-none"
            >
              <XIcon className="size-4" />
              <span className="sr-only">Close</span>
            </SheetClose>
          )}
        </DialogPrimitive>
      </Modal>
    </DialogOverlay>
  )
}

function SheetHeader({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      data-slot="sheet-header"
      className={cn("flex flex-col gap-1.5 p-4", className)}
      {...props}
    />
  )
}

function SheetFooter({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      data-slot="sheet-footer"
      className={cn("mt-auto flex flex-col gap-2 p-4", className)}
      {...props}
    />
  )
}

function SheetTitle({
  className,
  ...props
}: React.ComponentProps<typeof Heading>) {
  return (
    <Heading
      slot="title"
      data-slot="sheet-title"
      className={cn("text-foreground font-semibold", className)}
      {...props}
    />
  )
}

function SheetDescription({
  className,
  ...props
}: React.ComponentProps<typeof Text>) {
  return (
    <Text
      slot="description"
      elementType="p"
      data-slot="sheet-description"
      className={cn("text-muted-foreground text-sm", className)}
      {...props}
    />
  )
}

export {
  Sheet,
  SheetTrigger,
  SheetClose,
  SheetHeader,
  SheetFooter,
  SheetTitle,
  SheetDescription,
}
