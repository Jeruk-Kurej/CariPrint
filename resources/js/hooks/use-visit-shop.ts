import { router } from '@inertiajs/react';
import { show } from '@/routes/directory';
import type { ShopSummary } from '@/types';

/** Membuka halaman sebuah tempat dari id-nya, misalnya saat titik di peta diklik. */
export function useVisitShop(shops: ShopSummary[]) {
    return (id: number) => {
        const shop = shops.find((item) => item.id === id);

        if (shop) {
            router.visit(show({ shop: shop.slug }).url);
        }
    };
}
