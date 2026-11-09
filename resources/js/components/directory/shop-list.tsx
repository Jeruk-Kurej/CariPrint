import { ShopCard } from '@/components/directory/shop-card';
import type { ShopSummary } from '@/types';

type Props = {
    shops: ShopSummary[];
    selectedId: number | null;
    onSelect: (id: number) => void;
};

export function ShopList({ shops, selectedId, onSelect }: Props) {
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
