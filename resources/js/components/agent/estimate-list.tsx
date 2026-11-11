import { formatRupiah } from '@/lib/format';

type Estimate = {
    name: string;
    slug: string;
    total: number | null;
    missing: string[];
};

/** Cheapest first. Shops that cannot print the request show "tidak tersedia" instead of a price. */
export function EstimateList({ estimates }: { estimates: Estimate[] }) {
    return (
        <ul className="divide-y rounded-xl border">
            {estimates.map((shop) => (
                <li
                    key={shop.slug}
                    className="flex items-center justify-between gap-3 px-4 py-3"
                >
                    <span className="font-medium">{shop.name}</span>
                    {shop.total === null ? (
                        <span className="text-sm text-muted-foreground">
                            Tidak tersedia
                        </span>
                    ) : (
                        <span className="text-base font-semibold">
                            {formatRupiah(shop.total)}
                        </span>
                    )}
                </li>
            ))}
        </ul>
    );
}
