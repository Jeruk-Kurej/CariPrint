import { router } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import { directorySearch, readDirectoryChoices } from '@/lib/directory-url';
import type { DirectoryChoices } from '@/lib/directory-url';
import { filterAndSortShops } from '@/lib/shop-filter';
import type { ServiceGroup, ShopSort } from '@/lib/shop-filter';
import type { ShopSummary } from '@/types';

/** Pencarian, urutan, dan filter disimpan di alamat halaman, supaya bisa dibagikan dan tetap ada saat kembali. */
export function useDirectoryFilters(
    shops: ShopSummary[],
    hasDistance: boolean,
) {
    const [choices, setChoices] = useState(() =>
        readDirectoryChoices(window.location.search),
    );
    const { query, openOnly, paper, color, services } = choices;

    useEffect(() => {
        const search = directorySearch(choices);

        if (search !== window.location.search) {
            router.replace({
                url: `${window.location.pathname}${search}`,
                preserveState: true,
                preserveScroll: true,
            });
        }
    }, [choices]);

    function update(changes: Partial<DirectoryChoices>) {
        setChoices((current) => ({ ...current, ...changes }));
    }

    function toggleService(group: ServiceGroup) {
        update({
            services: services.includes(group)
                ? services.filter((item) => item !== group)
                : [...services, group],
        });
    }

    const maxKm = hasDistance ? choices.maxKm : null;
    const wanted = choices.sort ?? 'distance';
    const sort: ShopSort =
        wanted === 'distance' && !hasDistance ? 'name' : wanted;
    const priceChoice = useMemo(() => ({ paper, color }), [paper, color]);

    const visibleShops = useMemo(
        () =>
            filterAndSortShops(shops, {
                openOnly,
                sort,
                query,
                services,
                maxKm,
                priceChoice,
            }),
        [shops, openOnly, sort, query, services, maxKm, priceChoice],
    );
    const openCount = shops.filter(
        (shop) => shop.openState !== 'closed',
    ).length;

    return {
        openOnly,
        query,
        sort,
        services,
        maxKm,
        priceChoice,
        update,
        toggleService,
        visibleShops,
        openCount,
    };
}

export type DirectoryFilters = ReturnType<typeof useDirectoryFilters>;
