import { EmptyState } from '@/components/directory/empty-state';
import { ShopCard } from '@/components/directory/shop-card';
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
        return <ShopListSkeleton count={Math.min(shops.length || 3, 4)} />;
    }

    if (shops.length === 0) {
        return (
            <EmptyState
                title="Belum ada tempat printing"
                hint="Data tempat printing sedang disiapkan."
            />
        );
    }

    return (
        <div className="grid gap-3">
            {shops.map((shop) => (
                <ShopCard
                    key={shop.id}
                    shop={shop}
                    selected={shop.id === selectedId}
                    onSelect={() => onSelect(shop.id)}
                />
            ))}
        </div>
    );
}
