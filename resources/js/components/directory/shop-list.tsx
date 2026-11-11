import { ShopListSkeleton } from '@/components/directory/shop-list-skeleton';
import { ShopRow } from '@/components/directory/shop-row';
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

    return (
        <div className="divide-y overflow-hidden rounded-lg border bg-card">
            {shops.map((shop) => (
                <ShopRow
                    key={shop.id}
                    shop={shop}
                    selected={shop.id === selectedId}
                    onSelect={() => onSelect(shop.id)}
                />
            ))}
        </div>
    );
}
