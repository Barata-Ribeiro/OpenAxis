import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { useDebounceCallback } from '@/hooks/use-debounce-callback';
import erp from '@/routes/erp';
import type { ScrollMeta } from '@/types';
import type { Product } from '@/types/erp/product';
import { InfiniteScroll, router, usePage } from '@inertiajs/react';
import { Check, ChevronsUpDown } from 'lucide-react';
import type { Dispatch, SetStateAction } from 'react';
import { Activity, useMemo, useState } from 'react';

interface NewInventoryInventorySelectComboboxProps {
    value: number | null;
    setValue: Dispatch<SetStateAction<number | null>>;
}

export default function NewInventoryInventorySelectCombobox({
    value,
    setValue,
}: Readonly<NewInventoryInventorySelectComboboxProps>) {
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState('');
    const { products } = usePage<{ products: ScrollMeta<Pick<Product, 'id' | 'name'>[]> }>().props;

    const selectedLabel = useMemo(() => {
        const defaultLabel = 'Select product...';
        if (!value) return defaultLabel;
        return products.data.find((p) => p.id === value)?.name ?? defaultLabel;
    }, [value, products.data]);

    const debouncedSearch = useDebounceCallback((q: string) => {
        const normalizedQ = q.trim();

        router.visit(erp.inventory.create(), {
            data: normalizedQ ? { search: normalizedQ } : {},
            replace: true,
            preserveState: true,
            preserveScroll: true,
            only: ['products'],
            reset: ['products'],
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
                        placeholder="Search products..."
                        className="h-9"
                        value={search}
                        onValueChange={(next) => {
                            setSearch(next);
                            debouncedSearch(next.trim());
                        }}
                    />

                    <CommandList>
                        <CommandEmpty>No product found.</CommandEmpty>
                        <CommandGroup>
                            <InfiniteScroll data="products" buffer={500} preserveUrl>
                                {products.data.map((product) => (
                                    <CommandItem
                                        key={product.id}
                                        value={product.id.toString()}
                                        onSelect={(currentValue) => {
                                            const id = Number.parseInt(currentValue, 10);
                                            setValue(id === value ? null : id);
                                            setOpen(false);
                                        }}
                                    >
                                        {product.name}
                                        <Activity mode={value === product.id ? 'visible' : 'hidden'}>
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
