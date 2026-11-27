import { colorChoiceLabels, paperLabels } from '@/lib/price-choice';
import type { ColorChoice, PaperChoice } from '@/lib/price-choice';
import { distanceOptionsKm, parseSort, serviceGroups } from '@/lib/shop-filter';
import type { ServiceGroup, ShopSort } from '@/lib/shop-filter';

export type DirectoryChoices = {
    query: string;
    openOnly: boolean;
    sort: ShopSort | null;
    paper: PaperChoice;
    color: ColorChoice;
    services: ServiceGroup[];
    maxKm: number | null;
};

function oneOf<T extends string>(
    labels: Record<T, unknown>,
    value: string | null,
): T | null {
    return value !== null && Object.hasOwn(labels, value) ? (value as T) : null;
}

export const noChoices: DirectoryChoices = {
    query: '',
    openOnly: false,
    sort: null,
    paper: 'any',
    color: 'any',
    services: [],
    maxKm: null,
};

/** Filter dibaca dari alamat halaman. Nilai yang tidak dikenal diabaikan. */
export function readDirectoryChoices(search: string): DirectoryChoices {
    const params = new URLSearchParams(search);
    const maxKm = Number(params.get('jarak'));

    return {
        query: params.get('q') ?? '',
        openOnly: params.get('buka') === '1',
        sort: parseSort(params.get('urut')),
        paper: oneOf(paperLabels, params.get('kertas')) ?? 'any',
        color: oneOf(colorChoiceLabels, params.get('warna')) ?? 'any',
        services: (params.get('layanan') ?? '')
            .split(',')
            .filter((group): group is ServiceGroup =>
                Object.hasOwn(serviceGroups, group),
            ),
        maxKm: distanceOptionsKm.includes(maxKm) ? maxKm : null,
    };
}

/** Hanya filter yang dipilih yang ditulis, supaya alamatnya pendek saat dibagikan. */
export function directorySearch(choices: DirectoryChoices): string {
    const entries = {
        q: choices.query.trim(),
        buka: choices.openOnly ? '1' : '',
        urut: choices.sort ?? '',
        kertas: choices.paper === 'any' ? '' : choices.paper,
        warna: choices.color === 'any' ? '' : choices.color,
        layanan: choices.services.join(','),
        jarak: choices.maxKm?.toString() ?? '',
    };
    const search = new URLSearchParams(
        Object.entries(entries).filter(([, value]) => value !== ''),
    ).toString();

    return search === '' ? '' : `?${search}`;
}
