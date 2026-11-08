import { ShopCard } from '@/components/directory/shop-card';
import type { ShopSummary } from '@/types';

export function ShopList({ shops }: { shops: ShopSummary[] }) {
    return (
        <div className="grid gap-3 sm:grid-cols-2">
            {shops.map((shop) => (
                <ShopCard key={shop.id} shop={shop} />
            ))}
        </div>
    );
}
