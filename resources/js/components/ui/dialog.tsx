import {
  Button,
  Dialog as DialogPrimitive,
  DialogTrigger as DialogTriggerPrimitive,
  Heading,
  Modal,
  ModalOverlay,
  Text,
} from "react-aria-components"
import { XIcon } from "lucide-react"
import * as React from "react"

import { Button as StyledButton } from "@/components/ui/button"
import { cn } from "@/lib/utils"

const DialogTrigger = DialogTriggerPrimitive

function DialogOverlay({
  className,
  children,
  ...props
}: Omit<React.ComponentProps<typeof ModalOverlay>, "className" | "children"> & {
  className?: string
  children?: React.ReactNode
}) {
  return (
    <ModalOverlay
      data-slot="dialog-overlay"
      isDismissable
      className={cn(
        "fixed inset-0 isolate z-50 bg-black/80 data-entering:animate-in data-entering:fade-in-0 data-exiting:animate-out data-exiting:fade-out-0",
        className
      )}
      {...props}
    >
      {children}
    </ModalOverlay>
  )
}

function DialogClose(props: React.ComponentProps<typeof StyledButton>) {
  return <StyledButton slot="close" data-slot="dialog-close" {...props} />
}

function Dialog({
  className,
  children,
  showCloseButton = true,
  ...props
}: Omit<React.ComponentProps<typeof DialogOverlay>, "children"> & {
  children?: React.ReactNode
  showCloseButton?: boolean
}) {
  return (
    <DialogOverlay {...props}>
      <Modal
        data-slot="dialog-content"
        className={cn(
          "bg-background data-entering:animate-in data-exiting:animate-out data-exiting:fade-out-0 data-entering:fade-in-0 data-exiting:zoom-out-95 data-entering:zoom-in-95 fixed top-[50%] left-[50%] grid w-full max-w-[calc(100%-2rem)] translate-x-[-50%] translate-y-[-50%] gap-4 rounded-lg border p-6 shadow-lg duration-200 sm:max-w-lg",
          className
        )}
      >
        <DialogPrimitive
          data-slot="dialog"
          className="grid [gap:inherit] outline-none"
        >
          {children}
          {showCloseButton && (
            <Button
              slot="close"
              data-slot="dialog-close"
              className="ring-offset-background focus:ring-ring bg-accent text-muted-foreground absolute top-4 right-4 rounded-xs opacity-70 transition-opacity hover:opacity-100 focus:ring-2 focus:ring-offset-2 focus:outline-hidden disabled:pointer-events-none [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4"
            >
              <XIcon aria-hidden="true" />
              <span className="sr-only">Close</span>
            </Button>
          )}
        </DialogPrimitive>
      </Modal>
    </DialogOverlay>
  )
}

function DialogHeader({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      data-slot="dialog-header"
      className={cn("flex flex-col gap-2 text-center sm:text-left", className)}
      {...props}
    />
  )
}

function DialogFooter({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      data-slot="dialog-footer"
      className={cn(
        "flex flex-col-reverse gap-2 sm:flex-row sm:justify-end",
        className
      )}
      {...props}
    />
  )
}

function DialogTitle({
  className,
  ...props
}: React.ComponentProps<typeof Heading>) {
  return (
    <Heading
      slot="title"
      data-slot="dialog-title"
      className={cn("text-lg leading-none font-semibold", className)}
      {...props}
    />
  )
}

function DialogDescription({
  className,
  ...props
}: React.ComponentProps<typeof Text>) {
  return (
    <Text
      slot="description"
      elementType="p"
      data-slot="dialog-description"
      className={cn("text-muted-foreground text-sm", className)}
      {...props}
    />
  )
}

export {
  Dialog,
  DialogClose,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogOverlay,
  DialogTitle,
  DialogTrigger,
}
