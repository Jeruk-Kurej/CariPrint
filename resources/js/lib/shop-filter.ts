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

const comparators: Record<
    ShopSort,
    (a: ShopSummary, b: ShopSummary) => number
> = {
    distance: ascendingWithMissingLast((shop) => shop.distanceKm),
    price: ascendingWithMissingLast((shop) => shop.startingPrice),
    name: (a, b) => a.name.localeCompare(b.name, 'id'),
};

/** "Masih buka" membuang tempat yang sedang tutup, tapi tetap menampilkan yang segera tutup. */
export function filterAndSortShops(
    shops: ShopSummary[],
    { openOnly, sort, query = '', services = [], maxKm = null }: Options,
): ShopSummary[] {
    const needle = query.trim().toLowerCase();
    const visible = shops.filter(
        (shop) =>
            (!openOnly || shop.openState !== 'closed') &&
            (needle === '' || shop.name.toLowerCase().includes(needle)) &&
            services.every((group) =>
                shop.services.some((name) =>
                    serviceGroups[group].services.includes(name),
                ),
            ) &&
            (maxKm === null ||
                (shop.distanceKm !== null && shop.distanceKm <= maxKm)),
    );

    return [...visible].sort(comparators[sort]);
}

export const sortLabels: Record<ShopSort, string> = {
    distance: 'Terdekat',
    price: 'Termurah',
    name: 'Nama',
};

export function parseSort(value: string | null): ShopSort | null {
    return value !== null && value in sortLabels ? (value as ShopSort) : null;
}
