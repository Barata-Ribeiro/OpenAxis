import NewAddressModal from '@/components/extras/new-address-modal';
import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { MapPinXInsideIcon } from 'lucide-react';

export default function EmptyAddress() {
    return (
        <Empty className="border border-dashed">
            <EmptyHeader>
                <EmptyMedia variant="icon">
                    <MapPinXInsideIcon aria-hidden />
                </EmptyMedia>
                <EmptyTitle>No Addresses Yet</EmptyTitle>
                <EmptyDescription>
                    You haven&apos;t created any addresses yet. Get started by registering your first address.
                </EmptyDescription>
            </EmptyHeader>
            <EmptyContent>
                <NewAddressModal />
            </EmptyContent>
        </Empty>
    );
}
