import { cn } from "cn"
import * as React from "react"
import {
    Cell as CellPrimitive,
    Column as ColumnPrimitive,
    Row as RowPrimitive,
    TableBody as TableBodyPrimitive,
    TableFooter as TableFooterPrimitive,
    TableHeader as TableHeaderPrimitive,
    Table as TablePrimitive,
    type CellProps,
    type ColumnProps,
    type RowProps,
    type TableBodyProps,
    type TableFooterProps,
    type TableHeaderProps,
    type TableProps,
} from "react-aria-components"

function Table({ className, ...props }: Readonly<TableProps>) {
  return (
    <div
      data-slot="table-container"
      className="relative w-full overflow-x-auto"
      role="region"
      aria-label={props["aria-label"]}
      tabIndex={0}
    >
      <TablePrimitive
        data-slot="table"
        className={cn("w-full caption-bottom text-sm", className)}
        {...props}
      />
    </div>
  )
}

function TableHeader<T>({ className, ...props }: Readonly<TableHeaderProps<T>>) {
  return (
    <TableHeaderPrimitive
      data-slot="table-header"
      className={cn("[&_tr]:border-b", className)}
      {...props}
    />
  )
}

function TableBody<T>({ className, ...props }: Readonly<TableBodyProps<T>>) {
  return (
    <TableBodyPrimitive
      data-slot="table-body"
      className={cn(
        "data-empty:h-24 data-empty:text-center [&_tr:last-child]:border-0",
        className
      )}
      {...props}
    />
  )
}

function TableFooter<T>({ className, ...props }: Readonly<TableFooterProps<T>>) {
  return (
    <TableFooterPrimitive
      data-slot="table-footer"
      className={cn(
        "border-t bg-muted/50 font-medium [&>tr]:last:border-b-0",
        className
      )}
      {...props}
    />
  )
}

function TableRow<T>({ className, ...props }: Readonly<RowProps<T>>) {
  return (
    <RowPrimitive
      data-slot="table-row"
      className={cn(
        "border-b transition-colors hover:bg-muted/50 has-aria-expanded:bg-muted/50 data-[state=selected]:bg-muted data-selected:bg-muted",
        className
      )}
      {...props}
    />
  )
}

function TableHead({ className, ...props }: Readonly<ColumnProps>) {
  return (
    <ColumnPrimitive
      data-slot="table-head"
      className={cn(
        "h-10 px-2 text-left align-middle font-medium whitespace-nowrap text-foreground has-data-[slot=checkbox]:pr-0 has-[[role=checkbox]]:pr-0",
        className
      )}
      {...props}
    />
  )
}

function TableCell({ className, ...props }: Readonly<CellProps>) {
  return (
    <CellPrimitive
      data-slot="table-cell"
      className={cn(
        "p-2 align-middle whitespace-nowrap has-data-[slot=checkbox]:pr-0 has-[[role=checkbox]]:pr-0",
        className
      )}
      {...props}
    />
  )
}

function TableCaption({
  className,
  ...props
}: React.ComponentProps<"figcaption">) {
  return (
    <figcaption
      data-slot="table-caption"
      className={cn(
        "mt-4 text-center text-sm text-muted-foreground",
        className
      )}
      {...props}
    />
  )
}

export {
    Table, TableBody, TableCaption, TableCell, TableFooter,
    TableHead, TableHeader, TableRow
}
