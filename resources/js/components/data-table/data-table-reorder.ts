import type { DroppableCollectionReorderEvent } from 'react-aria-components';

export function reorderItems<T>(
    items: T[],
    getKey: (item: T) => string,
    event: Pick<DroppableCollectionReorderEvent, 'keys' | 'target'>,
): T[] {
    const moved = items.filter((item) => event.keys.has(getKey(item)));
    const remaining = items.filter((item) => !event.keys.has(getKey(item)));
    const targetIndex = remaining.findIndex((item) => getKey(item) === event.target.key);
    if (targetIndex < 0 || moved.length === 0) return items;

    remaining.splice(targetIndex + (event.target.dropPosition === 'after' ? 1 : 0), 0, ...moved);
    return remaining;
}
