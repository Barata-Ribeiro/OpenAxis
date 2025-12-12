import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { RouteDefinition } from '@/wayfinder';
import { Link } from '@inertiajs/react';
import { FileDownIcon, FileSpreadsheet, FileTextIcon } from 'lucide-react';

interface DataTableExportDataProps {
    csv?: RouteDefinition<'get'>;
    pdf?: RouteDefinition<'get'>;
}

export default function DataTableExportData({ csv, pdf }: Readonly<DataTableExportDataProps>) {
    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="outline" aria-label="Choose how to export data" title="Choose how to export data">
                    <FileDownIcon aria-hidden />
                    Export
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-[150px]">
                <DropdownMenuLabel>Exportables</DropdownMenuLabel>
                <DropdownMenuSeparator />
                {csv && (
                    <DropdownMenuItem className="w-full" disabled={!csv} asChild>
                        <Link href={csv} as="button" target="_blank" aria-label="Export as CSV" title="Export as CSV">
                            <FileSpreadsheet aria-hidden />
                            CSV
                        </Link>
                    </DropdownMenuItem>
                )}

                {pdf && (
                    <DropdownMenuItem className="w-full" disabled={!pdf} asChild>
                        <Link href={pdf} as="button" target="_blank" aria-label="Export as PDF" title="Export as PDF">
                            <FileTextIcon aria-hidden /> PDF
                        </Link>
                    </DropdownMenuItem>
                )}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
