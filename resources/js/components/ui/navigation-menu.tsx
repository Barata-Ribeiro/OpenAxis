import { ChevronDownIcon } from "lucide-react";
import type React from "react";
import { createContext, isValidElement, useContext } from "react";
import {
  Button as ButtonPrimitive,
  composeRenderProps,
  DialogTrigger,
  type DialogTriggerProps,
  Link as LinkPrimitive,
  type LinkProps,
  OverlayTriggerStateContext,
  Popover as PopoverPrimitive,
} from "react-aria-components";
import { cn } from "@/lib/utils";
import { cva } from "class-variance-authority";

export const NavigationMenu = (props: React.ComponentProps<"nav">) => {
  const { className, ...rest } = props;

  return (
    <nav
      className={cn(
        "group/navigation-menu relative flex max-w-max flex-1 items-center justify-center",
        className
      )}
      data-slot="navigation-menu"
      {...rest}
    />
  );
};

export const NavigationMenuList = (props: React.ComponentProps<"ul">) => {
  const { className, ...rest } = props;

  return (
    <ul
      className={cn(
        "gap-0 group flex flex-1 list-none items-center justify-center",
        className
      )}
      data-slot="navigation-menu-list"
      {...rest}
    />
  );
};

export const NavigationMenuItem = (
  props: DialogTriggerProps & { className?: string }
) => {
  const { className, children, ...rest } = props;
  // An item that is a single link opens nothing. Inside a DialogTrigger the
  // link would take the trigger's props and report itself collapsed.
  const linkOnly =
    isValidElement(children) && children.type === NavigationMenuLink;

  return (
    <li
      className={cn("relative", className)}
      data-slot="navigation-menu-item"
    >
      {linkOnly ? (
        children
      ) : (
        <DialogTrigger {...rest}>{children}</DialogTrigger>
      )}
    </li>
  );
};

export const navigationMenuTriggerStyle = cva(
  "hover:bg-muted focus:bg-muted aria-expanded:hover:bg-muted aria-expanded:focus:bg-muted aria-expanded:bg-muted/50 focus-visible:ring-ring/50 rounded-lg px-2.5 py-1.5 text-sm font-medium transition-all focus-visible:ring-3 focus-visible:outline-1 disabled:opacity-50 group/navigation-menu-trigger inline-flex h-9 w-max items-center justify-center outline-none disabled:pointer-events-none",
);

export const NavigationMenuTrigger = (
  props: React.ComponentProps<typeof ButtonPrimitive>
) => {
  const { className, children, ...rest } = props;

  return (
    <ButtonPrimitive
      className={composeRenderProps(className, (className) =>
        cn(navigationMenuTriggerStyle(), className)
      )}
      data-slot="navigation-menu-trigger"
      {...rest}
    >
      {composeRenderProps(children, (rendered) => (
        <>
          {rendered}
          <ChevronDownIcon
            aria-hidden="true"
            className="relative top-px ms-1 size-3 transition duration-300 group-aria-expanded/navigation-menu-trigger:rotate-180"
          />
        </>
      ))}
    </ButtonPrimitive>
  );
};

const NavigationMenuPopoverContext = createContext<
  React.ContextType<typeof OverlayTriggerStateContext>
>(null);

export const NavigationMenuContent = (
  props: Omit<React.ComponentProps<typeof PopoverPrimitive>, "className"> & {
    className?: string;
  }
) => {
  const { className, children, placement = "bottom start", offset = 6, ...rest } = props;
  const disclosure = useContext(OverlayTriggerStateContext);

  return (
    <PopoverPrimitive
      className={cn(
        "z-50 max-w-[calc(100vw-2rem)] origin-(--trigger-anchor-point) overflow-hidden rounded-lg border bg-popover p-1 text-popover-foreground shadow outline-none data-entering:animate-in data-entering:fade-in-0 data-entering:zoom-in-95 data-exiting:animate-out data-exiting:fade-out-0 data-exiting:zoom-out-95",
        className
      )}
      data-slot="navigation-menu-content"
      offset={offset}
      placement={placement}
      {...rest}
    >
      {composeRenderProps(children, (children) => (
        <NavigationMenuPopoverContext.Provider value={disclosure}>
          {children}
        </NavigationMenuPopoverContext.Provider>
      ))}
    </PopoverPrimitive>
  );
};

export const NavigationMenuLink = (
  props: LinkProps & {
    /**
     * Close the panel the link sits in when it's pressed. A press that opens
     * the link in a new tab (⌘ or Ctrl held) leaves the panel open.
     *
     * @default true
     */
    closeOnClick?: boolean;
  }
) => {
  const { className, closeOnClick = true, onPress, ...rest } = props;
  // Only links inside a navigation popover close its disclosure.
  const item = useContext(NavigationMenuPopoverContext);

  return (
    <LinkPrimitive
      className={composeRenderProps(className, (className) => cn("data-current:focus:bg-muted data-current:hover:bg-muted data-current:bg-muted/50 focus-visible:ring-ring/50 hover:bg-muted focus:bg-muted flex items-center gap-2 rounded-lg p-2 text-sm transition-all outline-none focus-visible:ring-3 focus-visible:outline-1 in-data-[slot=navigation-menu-content]:rounded-md [&_svg:not([class*='size-'])]:size-4", className))}
      data-slot="navigation-menu-link"
      onPress={(event) => {
        onPress?.(event);
        // A panel closes only on Escape or a press outside it, and with
        // client-side routing the page changes under it: close it here.
        if (closeOnClick && !event.metaKey && !event.ctrlKey && !event.shiftKey && !event.altKey) {
          item?.close();
        }
      }}
      {...rest}
    />
  );
};

export const NavigationMenuIndicator = (props: React.ComponentProps<"div">) => {
  const { className, ...rest } = props;
  const state = useContext(OverlayTriggerStateContext);

  return (
    <div
      className={cn(
        "hidden data-open:flex data-open:animate-in data-open:fade-in-0 top-full z-1 h-1.5 items-end justify-center overflow-hidden",
        className
      )}
      data-slot="navigation-menu-indicator"
      aria-hidden="true"
      data-open={state?.isOpen || undefined}
      {...rest}
    >
      <div className="bg-border rounded-tl-sm shadow-md relative top-[60%] h-2 w-2 rotate-45" />
    </div>
  );
};

export const NavigationMenuViewport = (props: React.ComponentProps<"div">) => {
  const { className, ...rest } = props;

  return (
    <div
      className={cn(
        "absolute inset-s-0 top-full isolate z-50 flex justify-center",
        className
      )}
      data-slot="navigation-menu-viewport"
      {...rest}
    />
  );
};

export const NavigationMenuPositioner = (
  props: React.ComponentProps<"div">
) => {
  const { className, ...rest } = props;

  return (
    <div
      className={cn("transition-[top,left,right,bottom] duration-300 ease-[cubic-bezier(0.22,1,0.36,1)] isolate z-50", className)}
      data-slot="navigation-menu-positioner"
      {...rest}
    />
  );
};
