import DataTable from '@/components/data-table/data-table';
import type { ServerTableData } from '@/components/data-table/data-table';
import { ArchiveLinks } from '@/components/erp/records/archive-links';
import { buttonVariants } from '@/components/ui/button';
import type { features } from '@/hooks/data-table/features';
import { usePermission } from '@/hooks/use-permission';
import { sections } from '@/lib/erp/settings/sections';
import type { SettingsSection } from '@/lib/erp/settings/sections';
import { Head } from '@inertiajs/react';
import { ModalLink } from '@inertiaui/modal-react';
import type { ColumnDef } from '@tanstack/react-table';

export type SettingsTableProps<TRow extends { id: number }> = {
    table: ServerTableData<TRow>;
    archived?: boolean;
};

export function SettingsTable<TRow extends { id: number }>({
    section,
    columns,
    table,
    archived,
}: Readonly<
    SettingsTableProps<TRow> & {
        section: SettingsSection;
        columns: ColumnDef<typeof features, TRow>[];
    }
>) {
    const { title, singular, routes, permission } = sections[section];
    const { can } = usePermission();
    const activeUrl = routes.index.url();
    const archivedUrl = routes.index.url({ query: { trashed: 'only' } });
    const url = archived ? archivedUrl : activeUrl;

    return (
        <section className="flex min-w-0 flex-col gap-5 p-4 sm:p-6">
            <Head title={title} />
            <header className="flex flex-wrap items-center justify-between gap-3">
                <h1 className="text-2xl font-semibold tracking-tight">{title}</h1>
                {can(`${permission}.create`) && (
                    <ModalLink href={routes.create.url()} className={buttonVariants()} navigate>
                        New {singular.toLowerCase()}
                    </ModalLink>
                )}
            </header>
            {archived !== undefined && (
                <ArchiveLinks activeUrl={activeUrl} archivedUrl={archivedUrl} archived={archived} />
            )}
            <DataTable key={url} columns={columns} result={table} url={url} label={title} />
        </section>
    );
}
