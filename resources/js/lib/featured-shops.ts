import type { ShopSummary } from '@/types';

export const FEATURED_COUNT = 3;

/** Yang buka dan paling murah jadi unggulan di hero. Sisanya masuk papan supaya tidak diulang. */
export function splitOpenShops(shops: ShopSummary[]) {
    const open = shops
        .filter((shop) => shop.openState !== 'closed')
        .sort(
            (a, b) =>
                (a.startingPrice ?? Infinity) - (b.startingPrice ?? Infinity),
        );

    return {
        featured: open.slice(0, FEATURED_COUNT),
        rest: open.slice(FEATURED_COUNT),
    };
}
