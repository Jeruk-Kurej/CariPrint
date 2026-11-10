import { PriceSourceBadge } from '@/components/directory/price-source-badge';
import { formatRupiah } from '@/lib/format';

type Price = {
    service: string;
    paperSize: string | null;
    colorMode: string | null;
    price: number;
    unit: string;
    source: 'verified' | 'estimated';
};

const colorLabel: Record<string, string> = {
    bw: 'Hitam-putih',
    color: 'Warna',
};
const unitLabel: Record<string, string> = {
    page: 'per halaman',
    item: 'per buku/lembar',
};

export function PriceTable({ prices }: { prices: Price[] }) {
    if (prices.length === 0) {
        return (
            <p className="text-sm text-muted-foreground">
                Harga belum tersedia.
            </p>
        );
    }

    return (
        <div className="overflow-x-auto rounded-xl border">
            <table className="w-full text-sm">
                <thead className="bg-muted text-left text-xs text-muted-foreground">
                    <tr>
                        <th className="px-3 py-2 font-medium">Layanan</th>
                        <th className="px-3 py-2 text-right font-medium">
                            Harga
                        </th>
                        <th className="px-3 py-2 font-medium">Sumber</th>
                    </tr>
                </thead>
                <tbody>
                    {prices.map((item, index) => (
                        <tr key={index} className="border-t">
                            <td className="px-3 py-2">
                                <div className="font-medium">
                                    {item.service}
                                </div>
                                <div className="text-xs text-muted-foreground">
                                    {[
                                        item.paperSize?.toUpperCase(),
                                        item.colorMode &&
                                            colorLabel[item.colorMode],
                                        unitLabel[item.unit],
                                    ]
                                        .filter(Boolean)
                                        .join(' · ')}
                                </div>
                            </td>
                            <td className="px-3 py-2 text-right text-base font-semibold">
                                {formatRupiah(item.price)}
                            </td>
                            <td className="px-3 py-2">
                                <PriceSourceBadge source={item.source} />
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
