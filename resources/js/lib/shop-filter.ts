import { anyPrice, priceFor } from '@/lib/price-choice';
import type { PriceChoice } from '@/lib/price-choice';
import { matchesShopQuery } from '@/lib/shop-search';
import type { ShopSummary } from '@/types';

export type ShopSort = 'distance' | 'price' | 'name';

export type ServiceGroup =
    | 'print'
    | 'photocopy'
    | 'binding'
    | 'scan'
    | 'laminating';

/** Nama layanan dari server yang masuk tiap kelompok. Tiga jenis jilid digabung jadi satu pilihan. */
export const serviceGroups: Record<
    ServiceGroup,
    { label: string; services: string[] }
> = {
    print: { label: 'Print', services: ['Print'] },
    photocopy: { label: 'Fotokopi', services: ['Photocopy'] },
    binding: {
        label: 'Jilid',
        services: ['SpiralBinding', 'TapeBinding', 'HardcoverBinding'],
    },
    scan: { label: 'Scan', services: ['Scan'] },
    laminating: { label: 'Laminating', services: ['Laminating'] },
};

export const distanceOptionsKm = [1, 2, 5];

type Options = {
    openOnly: boolean;
    sort: ShopSort;
    query?: string;
    /** Tempat harus menyediakan semua kelompok layanan yang dipilih. */
    services?: ServiceGroup[];
    /** Batas jarak dalam km. Diabaikan kalau null. */
    maxKm?: number | null;
    /** Kertas dan warna yang dipakai untuk urutan Termurah. */
    priceChoice?: PriceChoice;
};

/** Tempat tanpa data jarak atau harga selalu ditaruh di bawah. */
export function ascendingWithMissingLast<T>(
    valueOf: (item: T) => number | null,
): (a: T, b: T) => number {
    return (a, b) => {
        const left = valueOf(a);
        const right = valueOf(b);

        if (left === null || right === null) {
            return Number(left === null) - Number(right === null);
        }

        return left - right;
    };
}

function comparator(
    sort: ShopSort,
    priceChoice: PriceChoice,
): (a: ShopSummary, b: ShopSummary) => number {
    if (sort === 'name') {
        return (a, b) => a.name.localeCompare(b.name, 'id');
    }

    return ascendingWithMissingLast((shop) =>
        sort === 'distance'
            ? shop.distanceKm
            : (priceFor(shop, priceChoice)?.price ?? null),
    );
}

/** "Masih buka" membuang tempat yang sedang tutup, tapi tetap menampilkan yang segera tutup. */
export function filterAndSortShops(
    shops: ShopSummary[],
    {
        openOnly,
        sort,
        query = '',
        services = [],
        maxKm = null,
        priceChoice = anyPrice,
    }: Options,
): ShopSummary[] {
    const visible = shops.filter(
        (shop) =>
            (!openOnly || shop.openState !== 'closed') &&
            matchesShopQuery(shop, query) &&
            services.every((group) =>
                shop.services.some((name) =>
                    serviceGroups[group].services.includes(name),
                ),
            ) &&
            (maxKm === null ||
                (shop.distanceKm !== null && shop.distanceKm <= maxKm)),
    );

    return [...visible].sort(comparator(sort, priceChoice));
}

export const sortLabels: Record<ShopSort, string> = {
    distance: 'Terdekat',
    price: 'Termurah',
    name: 'Nama',
};

export function parseSort(value: string | null): ShopSort | null {
    return value !== null && value in sortLabels ? (value as ShopSort) : null;
}

/** Kelompok layanan yang disediakan sedikitnya satu tempat, sesuai urutan di serviceGroups. */
export function offeredServiceGroups(shops: ShopSummary[]): ServiceGroup[] {
    return (Object.keys(serviceGroups) as ServiceGroup[]).filter((group) =>
        shops.some((shop) =>
            shop.services.some((name) =>
                serviceGroups[group].services.includes(name),
            ),
        ),
    );
}
