import { useEffect, useRef, useState } from 'react';
import { FlyerCard } from '@/components/directory/flyer-card';
import { ShopListSkeleton } from '@/components/directory/shop-list-skeleton';
import { Pagination } from '@/components/pagination';
import type { PriceChoice } from '@/lib/price-choice';
import type { ShopSummary } from '@/types';

const PAGE_SIZE = 12;

type Props = {
    shops: ShopSummary[];
    priceChoice: PriceChoice;
    selectedId: number | null;
    onSelect: (id: number) => void;
    sorting?: boolean;
};

/** Menampilkan 12 tempat per halaman supaya halaman tetap ringan dan pendek di ponsel. Tempat yang dipilih dari peta membuka halamannya. */
export function ShopList({
    shops,
    priceChoice,
    selectedId,
    onSelect,
    sorting = false,
}: Props) {
    const [page, setPage] = useState(1);
    const top = useRef<HTMLDivElement>(null);
    const shopsRef = useRef(shops);

    shopsRef.current = shops;
    const totalPages = Math.max(1, Math.ceil(shops.length / PAGE_SIZE));

    useEffect(() => setPage(1), [shops]);

    useEffect(() => {
        const index = shopsRef.current.findIndex(
            (shop) => shop.id === selectedId,
        );

        if (index >= 0) {
            setPage(Math.floor(index / PAGE_SIZE) + 1);
        }
    }, [selectedId]);

    function goTo(next: number) {
        setPage(next);
        top.current?.scrollIntoView({ block: 'start' });
    }

    if (sorting) {
        return <ShopListSkeleton count={Math.min(shops.length || 4, 4)} />;
    }

    const start = (Math.min(page, totalPages) - 1) * PAGE_SIZE;

    return (
        <div ref={top} className="flex scroll-mt-20 flex-col gap-6">
            <p className="text-sm text-muted-foreground">
                Menampilkan {start + 1}–
                {Math.min(start + PAGE_SIZE, shops.length)} dari {shops.length}{' '}
                tempat
            </p>
            <div className="grid gap-6 pt-3 sm:grid-cols-2">
                {shops.slice(start, start + PAGE_SIZE).map((shop) => (
                    <FlyerCard
                        key={shop.id}
                        shop={shop}
                        priceChoice={priceChoice}
                        selected={shop.id === selectedId}
                        onSelect={onSelect}
                    />
                ))}
            </div>
            <Pagination
                page={Math.min(page, totalPages)}
                totalPages={totalPages}
                onChange={goTo}
            />
        </div>
    );
}
