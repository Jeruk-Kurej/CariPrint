import { useEffect, useState } from 'react';
import { FlyerCard } from '@/components/directory/flyer-card';
import { ShopListSkeleton } from '@/components/directory/shop-list-skeleton';
import { Button } from '@/components/ui/button';
import type { ShopSummary } from '@/types';

const PAGE_SIZE = 24;

type Props = {
    shops: ShopSummary[];
    selectedId: number | null;
    onSelect: (id: number) => void;
    sorting?: boolean;
};

/** Menampilkan 24 tempat dulu supaya halaman tetap ringan di ponsel. Tempat yang dipilih dari peta selalu ikut tampil. */
export function ShopList({
    shops,
    selectedId,
    onSelect,
    sorting = false,
}: Props) {
    const [visible, setVisible] = useState(PAGE_SIZE);

    useEffect(() => setVisible(PAGE_SIZE), [shops]);

    if (sorting) {
        return <ShopListSkeleton count={Math.min(shops.length || 4, 4)} />;
    }

    const selectedIndex = shops.findIndex((shop) => shop.id === selectedId);
    const count = Math.max(visible, selectedIndex + 1);
    const remaining = shops.length - count;

    return (
        <div className="flex flex-col gap-6">
            <div className="grid gap-6 pt-3 sm:grid-cols-2">
                {shops.slice(0, count).map((shop) => (
                    <FlyerCard
                        key={shop.id}
                        shop={shop}
                        selected={shop.id === selectedId}
                        onSelect={onSelect}
                    />
                ))}
            </div>
            {remaining > 0 && (
                <Button
                    variant="outline"
                    className="self-center"
                    onClick={() => setVisible(count + PAGE_SIZE)}
                >
                    Tampilkan lebih banyak ({remaining} lagi)
                </Button>
            )}
        </div>
    );
}
