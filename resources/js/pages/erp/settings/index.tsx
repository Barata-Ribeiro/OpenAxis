import DataTable, { type ServerTableData } from '@/components/data-table/data-table';
import { ErpFee } from '@/components/erp/format';
import { actionColumn, ErpLink, ErpPage, humanize, tableColumn } from '@/components/erp/page';
import { sections, type SettingsSection } from '@/lib/erp-settings';
import { dashboard } from '@/routes';
import { Activity } from 'react';

type RecordRow = {
    id: number;
    name: string;
    email: string;
    code: string;
    fee_type: string;
    fee_rate: number;
    quantity_decimals: number;
    is_active: boolean;
    is_system: boolean;
};

const column = tableColumn<RecordRow>;
const active = column('is_active', 'Active', 'number', (row) => (row.is_active ? 'Yes' : 'No'));
const code = column('code', 'Code');
const extraColumns = {
    'payment-methods': [
        column('fee_type', 'Fee type', 'text', (row) => humanize(row.fee_type)),
        column('fee_rate', 'Fee rate', 'number', (row) => <ErpFee rate={row.fee_rate} type={row.fee_type} />),
        active,
    ],
    'expense-categories': [column('is_system', 'System category', 'number', (row) => (row.is_system ? 'Yes' : 'No'))],
    categories: [],
    units: [code, column('quantity_decimals', 'Quantity decimals', 'number')],
    locations: [code, active],
    users: [column('email', 'Email')],
    roles: [],
};

export default function SettingsIndex({
    section,
    table,
}: Readonly<{ section: SettingsSection; table: ServerTableData<RecordRow> }>) {
    const { title, singular, permission, routes } = sections[section];
    const columns = [
        column('name', 'Name'),
        ...extraColumns[section],
        actionColumn<RecordRow>((row) => {
            const protectedRole = section === 'roles' && row.name === 'super-admin';
            const protectedCategory = section === 'expense-categories' && row.is_system;

            return (
                <>
                    <ErpLink href={routes.show.url(row.id)} permission={`${permission}.show`} compact modal>
                        View
                    </ErpLink>

                    <Activity mode={!protectedRole ? 'visible' : 'hidden'}>
                        <ErpLink href={routes.edit.url(row.id)} permission={`${permission}.edit`} compact modal>
                            Edit
                        </ErpLink>
                    </Activity>

                    <Activity mode={!protectedRole && !protectedCategory ? 'visible' : 'hidden'}>
                        <ErpLink href={routes.archive.url(row.id)} permission={`${permission}.destroy`} compact modal>
                            {section === 'users' || section === 'roles' ? 'Delete' : 'Archive'}
                        </ErpLink>
                    </Activity>
                </>
            );
        }),
    ];

    return (
        <ErpPage
            title={title}
            actions={
                <ErpLink href={routes.create.url()} permission={`${permission}.create`} modal primary>
                    New {singular.toLowerCase()}
                </ErpLink>
            }
        >
            <DataTable key={section} columns={columns} result={table} url={routes.index.url()} label={title} />
        </ErpPage>
    );
}

SettingsIndex.layout = ({ section }: { section: SettingsSection }) => ({
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: sections[section].title, href: sections[section].routes.index() },
    ],
});
