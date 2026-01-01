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
}

export default function PartnerSelectCombobox({ value, setValue, route }: Readonly<PartnerSelectComboboxProps>) {
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState('');
    const { suppliers } = usePage<{ suppliers: ScrollMeta<Pick<Partner, 'id' | 'name'>[]> }>().props;

    const selectedLabel = useMemo(() => {
        const defaultLabel = 'Select supplier...';
        if (!value) return defaultLabel;
        return suppliers.data.find((p) => p.id === value)?.name ?? defaultLabel;
    }, [value, suppliers.data]);

    const debouncedSearch = useDebounceCallback((q: string) => {
        const normalizedQ = q.trim();

        router.visit(route, {
            data: normalizedQ ? { search: `partner:${normalizedQ}` } : {},
            replace: true,
            preserveState: Boolean(normalizedQ),
            preserveScroll: true,
            only: ['suppliers'],
            reset: ['suppliers'],
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
                        placeholder="Search suppliers..."
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
                            <InfiniteScroll data="suppliers" buffer={500} preserveUrl>
                                {suppliers.data.map((supplier) => (
                                    <CommandItem
                                        key={supplier.id}
                                        value={supplier.id.toString()}
                                        onSelect={(currentValue) => {
                                            const id = Number.parseInt(currentValue, 10);
                                            setValue(id === value ? null : id);
                                            setOpen(false);
                                        }}
                                    >
                                        {supplier.name}
                                        <Activity mode={value === supplier.id ? 'visible' : 'hidden'}>
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
