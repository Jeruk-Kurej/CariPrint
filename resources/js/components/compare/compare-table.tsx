import { Link } from '@inertiajs/react';
import { OpenStatusBadge } from '@/components/directory/open-status-badge';
import { useScrollHint } from '@/hooks/use-scroll-hint';
import { formatRupiah } from '@/lib/format';
import { colorLabel, serviceLabel, unitLabel } from '@/lib/labels';
import { show } from '@/routes/directory';
import type { OpenState } from '@/types';

export type CompareShop = {
    id: number;
    name: string;
    slug: string;
    openState: OpenState;
};

export type CompareRow = {
    service: string;
    paperSize: string | null;
    colorMode: string | null;
    unit: string;
    prices: (number | null)[];
    cheapest: number[];
};

function rowTitle(row: CompareRow): string {
    return serviceLabel(row.service);
}

function rowDetail(row: CompareRow): string {
    return [
        row.paperSize?.toUpperCase(),
        row.colorMode && colorLabel(row.colorMode),
        unitLabel(row.unit),
    ]
        .filter(Boolean)
        .join(' · ');
}

function cheapestForVisible(
    row: CompareRow,
    shopIndices: number[],
): Set<number> {
    const available = shopIndices
        .map((index) => ({ index, price: row.prices[index] }))
        .filter(
            (item): item is { index: number; price: number } =>
                item.price !== null,
        );

    if (available.length < 2) {
        return new Set();
    }

    const lowest = Math.min(...available.map((item) => item.price));

    return new Set(
        available
            .filter((item) => item.price === lowest)
            .map((item) => item.index),
    );
}

/**
 * Satu baris per tempat printing, satu kolom per layanan. Dengan orientasi ini,
 * penambahan tempat baru selalu memanjang ke bawah, bukan melebar ke kanan.
 * Kalau kolomnya tidak muat, tabel bisa digeser dan ada petunjuk di tepi kanan serta di bawah tabel.
 */
export function CompareTable({
    shops,
    shopIndices,
    rows,
}: {
    shops: CompareShop[];
    shopIndices: number[];
    rows: CompareRow[];
}) {
    const scroll = useScrollHint<HTMLDivElement>();
    const cheapestByRow = rows.map((row) =>
        cheapestForVisible(row, shopIndices),
    );

    return (
        <div className="flex flex-col gap-2">
            <div className="relative overflow-hidden rounded-lg border bg-card">
                <div
                    ref={scroll.ref}
                    className="max-w-full overflow-x-auto [contain:paint]"
                >
                    <table className="w-full border-collapse text-sm">
                        <thead>
                            <tr className="bg-muted text-left">
                                <th
                                    scope="col"
                                    className="sticky left-0 z-10 min-w-28 bg-muted px-3 py-3 font-medium text-muted-foreground md:min-w-56 md:px-4"
                                >
                                    Tempat printing ({shopIndices.length})
                                </th>
                                {rows.map((row) => (
                                    <th
                                        key={`${row.service}-${row.paperSize}-${row.colorMode}-${row.unit}`}
                                        scope="col"
                                        className="min-w-24 px-3 py-3 text-left font-normal md:min-w-36 md:px-4 md:whitespace-nowrap"
                                    >
                                        <p className="font-display font-semibold text-foreground">
                                            {rowTitle(row)}
                                        </p>
                                        <p className="text-xs text-muted-foreground">
                                            {rowDetail(row)}
                                        </p>
                                    </th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {shopIndices.map((shopIndex) => {
                                const shop = shops[shopIndex];

                                return (
                                    <tr
                                        key={shop.id}
                                        className="group border-t transition-colors hover:bg-secondary"
                                    >
                                        <th
                                            scope="row"
                                            className="sticky left-0 z-10 bg-card px-3 py-3 text-left font-normal transition-colors group-hover:bg-secondary md:px-4"
                                        >
                                            <div className="flex flex-col items-start gap-1.5">
                                                <Link
                                                    href={
                                                        show({
                                                            shop: shop.slug,
                                                        }).url
                                                    }
                                                    className="font-display font-semibold text-foreground hover:underline"
                                                >
                                                    {shop.name}
                                                </Link>
                                                <OpenStatusBadge
                                                    state={shop.openState}
                                                />
                                            </div>
                                        </th>
                                        {rows.map((row, rowIndex) => {
                                            const price = row.prices[shopIndex];
                                            const isCheapest =
                                                cheapestByRow[rowIndex].has(
                                                    shopIndex,
                                                );

                                            return (
                                                <td
                                                    key={`${row.service}-${row.paperSize}-${row.colorMode}-${row.unit}`}
                                                    className="px-3 py-3 text-left whitespace-nowrap tabular-nums md:px-4"
                                                >
                                                    {price === null ? (
                                                        <span className="text-muted-foreground">
                                                            –
                                                        </span>
                                                    ) : (
                                                        <span
                                                            className={`inline-block rounded-sm px-1.5 font-display text-lg font-bold ${isCheapest ? 'bg-highlight text-highlight-foreground' : ''}`}
                                                        >
                                                            {formatRupiah(
                                                                price,
                                                            )}
                                                            {isCheapest && (
                                                                <span className="sr-only">
                                                                    , paling
                                                                    murah
                                                                </span>
                                                            )}
                                                        </span>
                                                    )}
                                                </td>
                                            );
                                        })}
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
                {scroll.hasMoreOnRight && (
                    <span
                        aria-hidden
                        className="pointer-events-none absolute inset-y-0 right-0 w-10 bg-gradient-to-l from-card to-transparent"
                    />
                )}
            </div>
            {scroll.scrollable && (
                <p className="text-sm text-muted-foreground">
                    Geser tabel ke samping untuk melihat kolom lainnya.
                </p>
            )}
        </div>
    );
}
