import HeadingSmall from '@/components/common/heading-small';
import AddressCard from '@/components/extras/address-card';
import EmptyAddress from '@/components/extras/empties/empty-address';
import NewAddressModal from '@/components/extras/new-address-modal';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import profile from '@/routes/profile';
import type { BreadcrumbItem } from '@/types';
import type { Address } from '@/types/application/address';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Addresses',
        href: profile.addresses().url,
    },
];

export default function Addresses({ addresses: initialAddresses }: Readonly<{ addresses: Address[] }>) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="My Addresses" />

            <SettingsLayout>
                <div className="w-full space-y-6">
                    <div className="mb-8 flex items-center justify-between">
                        <HeadingSmall
                            title="My Addresses"
                            description="Manage all your billing and shipping addresses"
                        />
                        <NewAddressModal />
                    </div>

                    {initialAddresses.length === 0 ? (
                        <EmptyAddress />
                    ) : (
                        <div className="grid grid-cols-1 gap-6">
                            {initialAddresses.map((address) => (
                                <AddressCard key={address.id} address={address} />
                            ))}
                        </div>
                    )}
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
