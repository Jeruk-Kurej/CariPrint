import { Link } from '@inertiajs/react';
import { formatRupiah } from '@/lib/format';
import { colorLabel, serviceLabel, unitLabel } from '@/lib/labels';
import { show } from '@/routes/directory';

export type CompareShop = { id: number; name: string; slug: string };

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

/** Satu baris per layanan, satu kolom per tempat. Sel termurah distabilo dan ditandai teks untuk pembaca layar. */
export function CompareTable({
    shops,
    rows,
}: {
    shops: CompareShop[];
    rows: CompareRow[];
}) {
    return (
        <div className="max-w-full overflow-x-auto rounded-lg border bg-card [contain:paint]">
            <table className="w-full border-collapse text-sm">
                <thead>
                    <tr className="bg-muted text-left">
                        <th
                            scope="col"
                            className="sticky left-0 z-10 min-w-40 bg-muted px-4 py-3 font-medium text-muted-foreground"
                        >
                            Layanan
                        </th>
                        {shops.map((shop) => (
                            <th
                                key={shop.id}
                                scope="col"
                                className="min-w-32 px-4 py-3 text-right font-display font-semibold"
                            >
                                <Link
                                    href={show({ shop: shop.slug }).url}
                                    className="hover:underline"
                                >
                                    {shop.name}
                                </Link>
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {rows.map((row) => (
                        <tr
                            key={`${row.service}-${row.paperSize}-${row.colorMode}-${row.unit}`}
                            className="border-t"
                        >
                            <th
                                scope="row"
                                className="sticky left-0 z-10 bg-card px-4 py-3 text-left font-normal"
                            >
                                <p className="font-medium">{rowTitle(row)}</p>
                                <p className="text-xs text-muted-foreground">
                                    {rowDetail(row)}
                                </p>
                            </th>
                            {row.prices.map((price, column) => (
                                <td
                                    key={shops[column].id}
                                    className="px-4 py-3 text-right tabular-nums"
                                >
                                    {price === null ? (
                                        <span className="text-muted-foreground">
                                            –
                                        </span>
                                    ) : (
                                        <span
                                            className={`inline-block rounded-sm px-1.5 font-display font-bold ${row.cheapest.includes(column) ? 'bg-highlight text-highlight-foreground' : ''}`}
                                        >
                                            {formatRupiah(price)}
                                            {row.cheapest.includes(column) && (
                                                <span className="sr-only">
                                                    , paling murah
                                                </span>
                                            )}
                                        </span>
                                    )}
                                </td>
                            ))}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
