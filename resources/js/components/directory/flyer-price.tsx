import { PriceSourceBadge } from '@/components/directory/price-source-badge';
import { formatRupiah } from '@/lib/format';
import { choiceLabel, priceFor } from '@/lib/price-choice';
import type { PriceChoice } from '@/lib/price-choice';
import type { ShopSummary } from '@/types';

/** Baris harga di pamflet: print termurah untuk pilihan kertas dan warna, beserta asal harganya. */
export function FlyerPrice({
    shop,
    choice,
}: {
    shop: ShopSummary;
    choice: PriceChoice;
}) {
    const price = priceFor(shop, choice);
    const label = choiceLabel(choice);

    return (
        <p className="flex items-center justify-between gap-2 px-4 pt-2.5 pb-2 sm:pt-3 sm:pb-2.5">
            {price === null ? (
                <span className="text-xs font-medium text-foreground/70">
                    {label
                        ? `Harga ${label} belum ada`
                        : 'Harga belum tersedia'}
                </span>
            ) : (
                <>
                    <span className="flex flex-col items-start gap-1 text-xs font-medium text-foreground/70">
                        Print {label && `${label} `}mulai dari
                        <PriceSourceBadge source={price.source} />
                    </span>
                    <span className="rounded-sm bg-highlight px-1.5 font-display text-lg font-extrabold tabular-nums sm:text-xl">
                        {formatRupiah(price.price)}
                    </span>
                </>
            )}
        </p>
    );
}
