import * as React from "react"

import { cn } from "@/lib/utils"

function Avatar({
  className,
  ...props
}: React.ComponentProps<"span">) {
  return (
    <span
      data-slot="avatar"
      className={cn(
        "relative flex size-8 shrink-0 overflow-hidden rounded-full",
        className
      )}
      {...props}
    />
  )
}

function AvatarImage({
  className,
  src,
  srcSet,
  alt = "",
  onLoad,
  onError,
  ...props
}: React.ComponentPropsWithoutRef<"img">) {
  const [loadedSource, setLoadedSource] = React.useState<string | null>(null)
  const source = src || srcSet
  const isLoaded = Boolean(source && loadedSource === source)

  if (!source) {
    return null
  }

  return (
    <img
      data-slot="avatar-image"
      data-loaded={isLoaded || undefined}
      className={cn("peer hidden aspect-square size-full data-loaded:block", className)}
      alt={alt}
      src={src}
      srcSet={srcSet}
      {...props}
      ref={(image) => {
        if (image?.complete && image.naturalWidth > 0) {
          setLoadedSource(source)
        }
      }}
      onLoad={(event) => {
        setLoadedSource(source)
        onLoad?.(event)
      }}
      onError={(event) => {
        setLoadedSource(null)
        onError?.(event)
      }}
    />
  )
}

function AvatarFallback({
  className,
  ...props
}: React.ComponentProps<"span">) {
  return (
    <span
      data-slot="avatar-fallback"
      className={cn(
        "bg-muted flex size-full items-center justify-center rounded-full peer-data-loaded:hidden",
        className
      )}
      {...props}
    />
  )
}

export { Avatar, AvatarImage, AvatarFallback }
