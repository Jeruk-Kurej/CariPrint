const rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

export function formatRupiah(amount: number): string {
    return rupiah.format(amount);
}

export function formatDistance(km: number): string {
    return km < 1 ? `${Math.round(km * 1000)} m` : `${km.toFixed(1)} km`;
}

const number = new Intl.NumberFormat('id-ID');

export function formatNumber(value: number): string {
    return number.format(value);
}

/** Menulis rentang halaman secara ringkas, misalnya "1–5, 8". */
export function formatPageRuns(runs: { from: number; to: number }[]): string {
    return runs
        .map((run) =>
            run.from === run.to ? `${run.from}` : `${run.from}–${run.to}`,
        )
        .join(', ');
}
