import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { useDebounceCallback } from '@/hooks/use-debounce-callback';
import type { ScrollMeta } from '@/types';
import type { Partner } from '@/types/erp/partner';
import type { RouteDefinition } from '@/wayfinder';
import { InfiniteScroll, router, usePage } from '@inertiajs/react';
import { Check, ChevronsUpDown } from 'lucide-react';
import type { Dispatch, SetStateAction } from 'react';
import { Activity, useMemo, useState } from 'react';

interface PartnerSelectComboboxProps {
    value: number | null;
    setValue: Dispatch<SetStateAction<number | null>>;
    route: RouteDefinition<'get'>;
    type: 'client' | 'supplier';
}

const _EMPTY: ScrollMeta<Pick<Partner, 'id' | 'name'>[]> = {
    data: [],
    path: '',
    per_page: 0,
};

export default function PartnerSelectCombobox({ value, setValue, route, type }: Readonly<PartnerSelectComboboxProps>) {
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState('');
    const pageProps = usePage<{
        clients?: ScrollMeta<Pick<Partner, 'id' | 'name'>[]>;
        suppliers?: ScrollMeta<Pick<Partner, 'id' | 'name'>[]>;
    }>().props;

    const partners = type === 'client' ? (pageProps.clients ?? _EMPTY) : (pageProps.suppliers ?? _EMPTY);

    const selectedLabel = useMemo(() => {
        const defaultLabel = type === 'client' ? 'Select client...' : 'Select supplier...';
        if (!value) return defaultLabel;
        return partners.data.find((p) => p.id === value)?.name ?? defaultLabel;
    }, [partners.data, type, value]);

    const debouncedSearch = useDebounceCallback((q: string) => {
        const normalizedQ = q.trim();

        router.visit(route, {
            data: normalizedQ ? { search: `partner:${normalizedQ}` } : {},
            replace: true,
            preserveState: Boolean(normalizedQ),
            preserveScroll: true,
            only: ['clients', 'suppliers'],
            reset: ['clients', 'suppliers'],
            preserveUrl: true,
        });
    }, 300);

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button variant="outline" role="combobox" aria-expanded={open} className="flex-1 justify-between">
                    {selectedLabel}
                    <ChevronsUpDown className="opacity-50" />
                </Button>
            </PopoverTrigger>

            <PopoverContent className="w-auto p-0" align="start">
                <Command shouldFilter={false}>
                    <CommandInput
                        placeholder={type === 'client' ? 'Search clients...' : 'Search suppliers...'}
                        className="h-9"
                        value={search}
                        onValueChange={(next) => {
                            setSearch(next);
                            debouncedSearch(next);
                        }}
                    />

                    <CommandList>
                        <CommandEmpty>No supplier found.</CommandEmpty>
                        <CommandGroup>
                            <InfiniteScroll data={type === 'client' ? 'clients' : 'suppliers'} buffer={500} preserveUrl>
                                {partners.data.map((partner) => (
                                    <CommandItem
                                        key={partner.id}
                                        value={partner.id.toString()}
                                        onSelect={(currentValue) => {
                                            const id = Number.parseInt(currentValue, 10);
                                            setValue(id === value ? null : id);
                                            setOpen(false);
                                        }}
                                    >
                                        {partner.name}
                                        <Activity mode={value === partner.id ? 'visible' : 'hidden'}>
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
