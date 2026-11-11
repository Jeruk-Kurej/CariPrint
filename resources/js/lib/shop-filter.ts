import type { ShopSummary } from '@/types';

export type ShopSort = 'distance' | 'price' | 'name';

type Options = { openOnly: boolean; sort: ShopSort; query?: string };

/** Tempat tanpa data jarak atau harga selalu ditaruh di bawah. */
function ascendingWithMissingLast(
    valueOf: (shop: ShopSummary) => number | null,
): (a: ShopSummary, b: ShopSummary) => number {
    return (a, b) => {
        const left = valueOf(a);
        const right = valueOf(b);

        if (left === null || right === null) {
            return Number(left === null) - Number(right === null);
        }

        return left - right;
    };
}

const comparators: Record<
    ShopSort,
    (a: ShopSummary, b: ShopSummary) => number
> = {
    distance: ascendingWithMissingLast((shop) => shop.distanceKm),
    price: ascendingWithMissingLast((shop) => shop.startingPrice),
    name: (a, b) => a.name.localeCompare(b.name, 'id'),
};

/** "Masih buka" membuang tempat yang sedang tutup, tapi tetap menampilkan yang segera tutup. */
export function filterAndSortShops(
    shops: ShopSummary[],
    { openOnly, sort, query = '' }: Options,
): ShopSummary[] {
    const needle = query.trim().toLowerCase();
    const visible = shops.filter(
        (shop) =>
            (!openOnly || shop.openState !== 'closed') &&
            (needle === '' || shop.name.toLowerCase().includes(needle)),
    );

    return [...visible].sort(comparators[sort]);
}
