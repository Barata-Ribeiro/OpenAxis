import categories from '@/routes/erp/settings/categories';
import expenseCategories from '@/routes/erp/settings/expense-categories';
import locations from '@/routes/erp/settings/locations';
import paymentMethods from '@/routes/erp/settings/payment-methods';
import roles from '@/routes/erp/settings/roles';
import units from '@/routes/erp/settings/units';
import users from '@/routes/erp/settings/users';
import { Boxes, ReceiptText, Ruler, ShieldCheck, Tags, Users, WalletCards } from 'lucide-react';

export const sections = {
    'payment-methods': {
        title: 'Payment methods',
        singular: 'Payment method',
        permission: 'payment-method',
        routes: paymentMethods,
        icon: WalletCards,
    },
    'expense-categories': {
        title: 'Expense categories',
        singular: 'Expense category',
        permission: 'expense-category',
        routes: expenseCategories,
        icon: ReceiptText,
    },
    categories: {
        title: 'Product categories',
        singular: 'Product category',
        permission: 'category',
        routes: categories,
        icon: Tags,
    },
    units: {
        title: 'Units of measure',
        singular: 'Unit of measure',
        permission: 'unit-of-measure',
        routes: units,
        icon: Ruler,
    },
    locations: {
        title: 'Storage locations',
        singular: 'Storage location',
        permission: 'storage-location',
        routes: locations,
        icon: Boxes,
    },
    users: { title: 'Users', singular: 'User', permission: 'user', routes: users, icon: Users },
    roles: { title: 'Roles', singular: 'Role', permission: 'role', routes: roles, icon: ShieldCheck },
} as const;

export type SettingsSection = keyof typeof sections;
