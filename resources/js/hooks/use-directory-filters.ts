import { useMemo, useState } from 'react';
import { filterAndSortShops, parseSort } from '@/lib/shop-filter';
import type { ServiceGroup, ShopSort } from '@/lib/shop-filter';
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

    const [services, setServices] = useState<ServiceGroup[]>([]);
    const [chosenMaxKm, setMaxKm] = useState<number | null>(null);

    function toggleService(group: ServiceGroup) {
        setServices((current) =>
            current.includes(group)
                ? current.filter((item) => item !== group)
                : [...current, group],
        );
    }

    const maxKm = hasDistance ? chosenMaxKm : null;
    const wanted = chosenSort ?? 'distance';
    const sort: ShopSort =
        wanted === 'distance' && !hasDistance ? 'name' : wanted;

    const visibleShops = useMemo(
        () =>
            filterAndSortShops(shops, {
                openOnly,
                sort,
                query,
                services,
                maxKm,
            }),
        [shops, openOnly, sort, query, services, maxKm],
    );
    const openCount = shops.filter(
        (shop) => shop.openState !== 'closed',
    ).length;

    return {
        openOnly,
        setOpenOnly,
        query,
        setQuery,
        sort,
        setSort,
        services,
        toggleService,
        maxKm,
        setMaxKm,
        visibleShops,
        openCount,
    };
}

export type DirectoryFilters = ReturnType<typeof useDirectoryFilters>;
