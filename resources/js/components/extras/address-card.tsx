import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import profile from '@/routes/profile';
import { Address } from '@/types/application/address';
import { addressTypeLabel } from '@/types/application/enums';
import { Link } from '@inertiajs/react';
import { MapPin, MoreVertical, Pencil, Star, StarIcon, Trash2 } from 'lucide-react';
import { Activity } from 'react';

const getTypeVariant = (type: Address['type']) => {
    switch (type) {
        case 'billing':
            return 'default';
        case 'shipping':
            return 'secondary';
        case 'billing_and_shipping':
            return 'outline';
        default:
            return 'outline';
    }
};

export default function AddressCard({ address }: Readonly<{ address: Address }>) {
    return (
        <Card className="relative overflow-hidden transition-shadow hover:shadow-md">
            <Activity mode={address.is_primary ? 'visible' : 'hidden'}>
                <div className="absolute top-0 right-0 size-0 border-t-40 border-l-40 border-t-primary border-l-transparent">
                    <StarIcon className="absolute top-[-36px] -right-[34px] size-4 fill-primary-foreground text-primary-foreground" />
                </div>
            </Activity>

            <CardHeader className="pb-3">
                <div className="flex items-start justify-between gap-2">
                    <div className="flex flex-wrap items-center gap-2">
                        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                            <MapPin className="h-5 w-5 text-muted-foreground" />
                        </div>
                        <div>
                            <h3 className="font-semibold text-card-foreground">{address.label ?? 'Untitled'}</h3>
                            <Badge variant={getTypeVariant(address.type)} className="mt-1">
                                {addressTypeLabel(address.type)}
                            </Badge>
                        </div>
                    </div>

                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="icon" className="size-8">
                                <MoreVertical size={16} />
                                <span className="sr-only">Open menu</span>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem className="gap-2">
                                <Pencil size={16} />
                                Edit
                            </DropdownMenuItem>
                            <Activity mode={address.is_primary ? 'hidden' : 'visible'}>
                                <DropdownMenuItem asChild>
                                    <Link
                                        href={profile.addresses.setPrimary(address.id)}
                                        method="patch"
                                        as="button"
                                        className="block w-full"
                                    >
                                        <Star size={16} />
                                        Set as Primary
                                    </Link>
                                </DropdownMenuItem>
                            </Activity>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem variant="destructive" asChild>
                                <Link
                                    className="block w-full"
                                    href={profile.addresses.destroy(address.id)}
                                    method="delete"
                                    as="button"
                                >
                                    <Trash2 aria-hidden size={16} />
                                    Delete
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </CardHeader>

            <CardContent>
                <address className="text-sm leading-relaxed text-muted-foreground not-italic">
                    <div className="space-y-1">
                        <p className="font-medium text-card-foreground">
                            {address.street}, {address.number}
                        </p>

                        {address.complement && <p>{address.complement}</p>}

                        <p>{address.neighborhood}</p>

                        <p>
                            {address.city}, {address.state} {address.postal_code}
                        </p>

                        <p>{address.country}</p>
                    </div>
                </address>
            </CardContent>
        </Card>
    );
}
