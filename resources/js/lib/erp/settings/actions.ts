import type { RowAction } from '@/components/data-table/cell-components';
import { Eye, PencilLine, RotateCcw, Trash } from 'lucide-react';

type RecordRoute = { url: (id: number) => string };
type SettingsRoutes = {
    show: RecordRoute;
    edit: RecordRoute;
    archive: RecordRoute;
    restore?: RecordRoute;
    confirmDestroy?: RecordRoute;
};

export function settingsActions(
    row: { id: number; deleted_at?: string | null },
    routes: SettingsRoutes,
    permission: string,
    { canEdit = true, canRemove = true }: { canEdit?: boolean; canRemove?: boolean } = {},
): (RowAction | false)[] {
    const id = row.id;

    return [
        { label: 'View', href: routes.show.url(id), permissions: [`${permission}.show`], icon: Eye },
        !row.deleted_at &&
            canEdit && {
                label: 'Edit',
                href: routes.edit.url(id),
                permissions: [`${permission}.edit`],
                icon: PencilLine,
            },
        !row.deleted_at &&
            canRemove && {
                label: routes.confirmDestroy ? 'Delete' : 'Archive',
                href: (routes.confirmDestroy ?? routes.archive).url(id),
                permissions: [`${permission}.destroy`],
                icon: Trash,
                destructive: true,
            },
        Boolean(row.deleted_at) &&
            routes.restore !== undefined && {
                label: 'Restore',
                href: routes.restore.url(id),
                method: 'patch',
                permissions: [`${permission}.destroy`],
                icon: RotateCcw,
            },
    ];
}
