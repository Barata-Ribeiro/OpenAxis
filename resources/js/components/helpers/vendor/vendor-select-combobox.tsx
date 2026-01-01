import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { useDebounceCallback } from '@/hooks/use-debounce-callback';
import type { ScrollMeta } from '@/types';
import type { Vendor } from '@/types/erp/vendor';
import type { RouteDefinition } from '@/wayfinder';
import { InfiniteScroll, router, usePage } from '@inertiajs/react';
import { Check, ChevronsUpDown } from 'lucide-react';
import type { Dispatch, SetStateAction } from 'react';
import { Activity, useMemo, useState } from 'react';

interface VendorSelectComboboxProps {
    value: number | null;
    setValue: Dispatch<SetStateAction<number | null>>;
    route: RouteDefinition<'get'>;
}

export default function VendorSelectCombobox({ value, setValue, route }: Readonly<VendorSelectComboboxProps>) {
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState('');
    const { vendors } = usePage<{ vendors: ScrollMeta<Pick<Vendor, 'id' | 'full_name'>[]> }>().props;

    const selectedLabel = useMemo(() => {
        const defaultLabel = 'Select vendor...';
        if (!value) return defaultLabel;
        return vendors.data.find((p) => p.id === value)?.full_name ?? defaultLabel;
    }, [value, vendors.data]);

    const debouncedSearch = useDebounceCallback((q: string) => {
        const normalizedQ = q.trim();

        router.visit(route, {
            data: normalizedQ ? { search: `vendor:${normalizedQ}` } : {},
            replace: true,
            preserveState: Boolean(normalizedQ),
            preserveScroll: true,
            only: ['vendors'],
            reset: ['vendors'],
            preserveUrl: true,
        });
    }, 300);
    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button variant="outline" role="combobox" aria-expanded={open} className="w-full justify-between">
                    {selectedLabel}
                    <ChevronsUpDown className="opacity-50" />
                </Button>
            </PopoverTrigger>

            <PopoverContent className="w-full p-0">
                <Command shouldFilter={false}>
                    <CommandInput
                        placeholder="Search vendors..."
                        className="h-9"
                        value={search}
                        onValueChange={(next) => {
                            setSearch(next);
                            debouncedSearch(next);
                        }}
                    />

                    <CommandList>
                        <CommandEmpty>No vendor found.</CommandEmpty>
                        <CommandGroup>
                            <InfiniteScroll data="vendors" buffer={500} preserveUrl>
                                {vendors.data.map((vendor) => (
                                    <CommandItem
                                        key={vendor.id}
                                        value={vendor.id.toString()}
                                        onSelect={(currentValue) => {
                                            const id = Number.parseInt(currentValue, 10);
                                            setValue(id === value ? null : id);
                                            setOpen(false);
                                        }}
                                    >
                                        {vendor.full_name}
                                        <Activity mode={value === vendor.id ? 'visible' : 'hidden'}>
                                            <Check aria-hidden className="ml-auto" />
                                        </Activity>
                                    </CommandItem>
                                ))}
                            </InfiniteScroll>
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
