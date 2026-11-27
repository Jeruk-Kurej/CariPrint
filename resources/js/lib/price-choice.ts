import type { PrintPrice, ShopSummary } from '@/types';

export const paperLabels = {
    any: 'Semua kertas',
    a4: 'A4',
    f4: 'F4',
    a3: 'A3',
};

export const colorChoiceLabels = {
    any: 'Semua warna',
    bw: 'Hitam-putih',
    color: 'Warna',
};

export type PaperChoice = keyof typeof paperLabels;
export type ColorChoice = keyof typeof colorChoiceLabels;
export type PriceChoice = { paper: PaperChoice; color: ColorChoice };

export const anyPrice: PriceChoice = { paper: 'any', color: 'any' };

/** Harga sudah dihitung server untuk tiap pilihan. Di sini cuma diambil. */
export function priceFor(
    shop: ShopSummary,
    { paper, color }: PriceChoice,
): PrintPrice | null {
    return shop.printPrices[`${paper}_${color}`] ?? null;
}

/** Misalnya "F4 hitam-putih". Kosong kalau kertas dan warna tidak dipilih. */
export function choiceLabel({ paper, color }: PriceChoice): string {
    return [
        paper === 'any' ? '' : paperLabels[paper],
        color === 'any' ? '' : colorChoiceLabels[color].toLowerCase(),
    ]
        .filter(Boolean)
        .join(' ');
}
