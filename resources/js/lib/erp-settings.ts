import categories from '@/routes/erp/settings/categories';
import expenseCategories from '@/routes/erp/settings/expense-categories';
import locations from '@/routes/erp/settings/locations';
import paymentMethods from '@/routes/erp/settings/payment-methods';
import roles from '@/routes/erp/settings/roles';
import units from '@/routes/erp/settings/units';
import users from '@/routes/erp/settings/users';

export const sections = {
    'payment-methods': {
        title: 'Payment methods',
        singular: 'Payment method',
        permission: 'payment-method',
        routes: paymentMethods,
    },
    'expense-categories': {
        title: 'Expense categories',
        singular: 'Expense category',
        permission: 'expense-category',
        routes: expenseCategories,
    },
    categories: {
        title: 'Product categories',
        singular: 'Product category',
        permission: 'category',
        routes: categories,
    },
    units: { title: 'Units of measure', singular: 'Unit of measure', permission: 'unit-of-measure', routes: units },
    locations: {
        title: 'Storage locations',
        singular: 'Storage location',
        permission: 'storage-location',
        routes: locations,
    },
    users: { title: 'Users', singular: 'User', permission: 'user', routes: users },
    roles: { title: 'Roles', singular: 'Role', permission: 'role', routes: roles },
} as const;

export type SettingsSection = keyof typeof sections;
