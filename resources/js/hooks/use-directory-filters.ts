import { useMemo, useState } from 'react';
import { filterAndSortShops, parseSort } from '@/lib/shop-filter';
import type { ShopSort } from '@/lib/shop-filter';
import type { ShopSummary } from '@/types';

/** Pencarian, urutan, dan filter bisa dibawa lewat alamat, misalnya dari chip di beranda. */
function initialParams(): URLSearchParams {
    return new URLSearchParams(window.location.search);
}

export function useDirectoryFilters(
    shops: ShopSummary[],
    hasDistance: boolean,
) {
    const [openOnly, setOpenOnly] = useState(
        () => initialParams().get('buka') === '1',
    );
    const [query, setQuery] = useState(() => initialParams().get('q') ?? '');
    const [chosenSort, setSort] = useState<ShopSort | null>(() =>
        parseSort(initialParams().get('urut')),
    );

    const wanted = chosenSort ?? 'distance';
    const sort: ShopSort =
        wanted === 'distance' && !hasDistance ? 'name' : wanted;

    const visibleShops = useMemo(
        () => filterAndSortShops(shops, { openOnly, sort, query }),
        [shops, openOnly, sort, query],
    );
    const openCount = shops.filter(
        (shop) => shop.openState !== 'closed',
    ).length;

    return {
        openOnly,
        toggleOpenOnly: () => setOpenOnly((on) => !on),
        query,
        setQuery,
        sort,
        setSort,
        visibleShops,
        openCount,
    };
}

export type DirectoryFilters = ReturnType<typeof useDirectoryFilters>;
