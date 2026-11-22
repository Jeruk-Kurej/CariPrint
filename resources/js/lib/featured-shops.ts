import type { ShopSummary } from '@/types';

export const FEATURED_COUNT = 3;

/** Yang buka dan sudah punya harga print, dari yang paling murah. Tiga teratas jadi unggulan di hero, sisanya masuk papan supaya tidak diulang. */
export function splitOpenShops(shops: ShopSummary[]) {
    const open = shops
        .filter(
            (shop) =>
                shop.openState !== 'closed' && shop.startingPrice !== null,
        )
        .sort((a, b) => (a.startingPrice ?? 0) - (b.startingPrice ?? 0));

    return {
        featured: open.slice(0, FEATURED_COUNT),
        rest: open.slice(FEATURED_COUNT),
    };
}
