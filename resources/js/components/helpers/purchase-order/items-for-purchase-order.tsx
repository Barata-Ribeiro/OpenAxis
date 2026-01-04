import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Input } from '@/components/ui/input';
import { Item, ItemActions, ItemContent, ItemDescription, ItemTitle } from '@/components/ui/item';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { useDebounceCallback } from '@/hooks/use-debounce-callback';
import { formatCurrency } from '@/lib/utils';
import type { ScrollMeta } from '@/types';
import type { Product } from '@/types/erp/product';
import type { RouteDefinition } from '@/wayfinder';
import { InfiniteScroll, router, usePage } from '@inertiajs/react';
import { Check, ChevronsUpDown, Trash2 } from 'lucide-react';
import type { Dispatch, SetStateAction } from 'react';
import { Activity, useMemo, useState } from 'react';

export type SelectedProduct = Pick<Product, 'id' | 'name' | 'selling_price'> & { quantity: number };

interface ItemsForPurchaseOrderProps {
    value: SelectedProduct[];
    setValue: Dispatch<SetStateAction<SelectedProduct[]>>;
    route: RouteDefinition<'get'>;
}

export default function ItemsForPurchaseOrder({ value, setValue, route }: Readonly<ItemsForPurchaseOrderProps>) {
    const { products } = usePage<{ products: ScrollMeta<Pick<Product, 'id' | 'name' | 'selling_price'>[]> }>().props;
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState('');
    const [selectedProductId, setSelectedProductId] = useState<number | null>(null);
    const [quantity, setQuantity] = useState<number>(1);

    const selectedLabel = useMemo(() => {
        const defaultLabel = 'Select product...';
        if (!selectedProductId) return defaultLabel;
        return products.data.find((p) => p.id === selectedProductId)?.name ?? defaultLabel;
    }, [selectedProductId, products.data]);

    const selectedProduct = useMemo(() => {
        if (!selectedProductId) return null;
        return products.data.find((p) => p.id === selectedProductId) ?? null;
    }, [selectedProductId, products.data]);

    const debouncedSearch = useDebounceCallback((q: string) => {
        const normalizedQ = q.trim();

        router.visit(route, {
            data: normalizedQ ? { search: `product:${normalizedQ}` } : {},
            replace: true,
            preserveState: Boolean(normalizedQ),
            preserveScroll: true,
            only: ['products'],
            reset: ['products'],
            preserveUrl: true,
        });
    }, 300);

    const addSelectedProduct = (): void => {
        if (!selectedProduct) return;

        const nextQuantity = Number.isFinite(quantity) ? Math.max(1, Math.trunc(quantity)) : 1;

        setValue((current) => {
            const existingIndex = current.findIndex((p) => p.id === selectedProduct.id);
            if (existingIndex === -1) return [...current, { ...selectedProduct, quantity: nextQuantity }];
            return current.map((p) => (p.id === selectedProduct.id ? { ...p, quantity: nextQuantity } : p));
        });

        setSelectedProductId(null);
        setQuantity(1);
        setSearch('');
    };

    return (
        <div className="space-y-4 border-y py-4">
            <div className="grid gap-3 sm:grid-cols-[1fr_140px_auto]">
                <Popover open={open} onOpenChange={setOpen}>
                    <PopoverTrigger asChild>
                        <Button
                            variant="outline"
                            role="combobox"
                            aria-expanded={open}
                            className="w-full justify-between"
                        >
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
                                    debouncedSearch(next);
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
                                                    setSelectedProductId((current) => (current === id ? null : id));
                                                    setOpen(false);
                                                }}
                                            >
                                                {product.name}
                                                <Activity
                                                    mode={selectedProductId === product.id ? 'visible' : 'hidden'}
                                                >
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

                <ButtonGroup>
                    <Input
                        type="number"
                        inputMode="numeric"
                        min={1}
                        step={1}
                        value={Number.isFinite(quantity) ? quantity : 1}
                        onChange={(e) => setQuantity(Number.parseInt(e.target.value || '1', 10))}
                        aria-label="Quantity"
                        disabled={!selectedProduct}
                    />

                    <Button type="button" onClick={addSelectedProduct} disabled={!selectedProduct}>
                        Add
                    </Button>
                </ButtonGroup>
            </div>

            {value.length > 0 ? (
                <div className="space-y-2">
                    {value.map((item) => (
                        <Item key={item.id} variant="outline">
                            <ItemContent className="min-w-0">
                                <ItemTitle className="truncate font-medium">{item.name}</ItemTitle>
                                <ItemDescription className="text-sm text-muted-foreground">
                                    {formatCurrency(item.selling_price)} per unit
                                </ItemDescription>
                            </ItemContent>

                            <ItemActions>
                                <ButtonGroup>
                                    <Input
                                        type="number"
                                        inputMode="numeric"
                                        min={1}
                                        step={1}
                                        value={item.quantity}
                                        onChange={(e) => {
                                            const nextQuantity = Math.max(
                                                1,
                                                Number.parseInt(e.target.value || '1', 10),
                                            );
                                            setValue((current) =>
                                                current.map((p) =>
                                                    p.id === item.id ? { ...p, quantity: nextQuantity } : p,
                                                ),
                                            );
                                        }}
                                        aria-label={`Quantity for '${item.name}'`}
                                    />

                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={() => setValue((current) => current.filter((p) => p.id !== item.id))}
                                        aria-label={`Remove '${item.name}'`}
                                        title={`Remove '${item.name}'`}
                                    >
                                        <Trash2 aria-hidden />
                                    </Button>
                                </ButtonGroup>
                            </ItemActions>
                        </Item>
                    ))}
                </div>
            ) : null}
        </div>
    );
}
