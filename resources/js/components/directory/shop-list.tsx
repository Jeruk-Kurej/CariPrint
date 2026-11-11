import { FlyerCard } from '@/components/directory/flyer-card';
import { ShopListSkeleton } from '@/components/directory/shop-list-skeleton';
import type { ShopSummary } from '@/types';

type Props = {
    shops: ShopSummary[];
    selectedId: number | null;
    onSelect: (id: number) => void;
    sorting?: boolean;
};

export function ShopList({
    shops,
    selectedId,
    onSelect,
    sorting = false,
}: Props) {
    if (sorting) {
        return <ShopListSkeleton count={Math.min(shops.length || 4, 4)} />;
    }

    return (
        <div className="grid gap-6 pt-3 sm:grid-cols-2">
            {shops.map((shop) => (
                <FlyerCard
                    key={shop.id}
                    shop={shop}
                    selected={shop.id === selectedId}
                    onSelect={onSelect}
                />
            ))}
        </div>
    );
}
