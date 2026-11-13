/**
 * Menilai apakah sebuah halaman perlu dicetak berwarna, dari piksel gambar halaman itu.
 *
 * Satu piksel dianggap berwarna kalau selisih kanal merah, hijau, dan birunya cukup besar. Abu-abu, hitam,
 * dan putih selisihnya nol, dan warna kertas yang agak kekuningan masih di bawah batas. Halaman dianggap
 * berwarna kalau luas piksel berwarna cukup besar, sehingga logo kecil atau tautan biru tidak ikut dihitung.
 */

/** Selisih terbesar antar-kanal (0-255) yang masih dianggap abu-abu. */
export const COLOR_CHANNEL_GAP = 40;

/** Bagian halaman yang harus berwarna supaya halaman itu dianggap perlu warna. */
export const COLOR_AREA_SHARE = 0.02;

/** Bagian piksel yang berwarna, dari data RGBA seperti hasil `getImageData`. */
export function coloredShare(rgba: Uint8ClampedArray): number {
    const pixels = rgba.length / 4;
    let colored = 0;

    for (let i = 0; i < rgba.length; i += 4) {
        const gap =
            Math.max(rgba[i], rgba[i + 1], rgba[i + 2]) -
            Math.min(rgba[i], rgba[i + 1], rgba[i + 2]);

        if (gap > COLOR_CHANNEL_GAP) {
            colored++;
        }
    }

    return pixels === 0 ? 0 : colored / pixels;
}

export function needsColor(rgba: Uint8ClampedArray): boolean {
    return coloredShare(rgba) > COLOR_AREA_SHARE;
}

