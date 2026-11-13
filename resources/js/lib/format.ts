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

/** Menulis nomor halaman yang berurutan sebagai rentang, misalnya [1, 2, 3, 7] menjadi "1–3, 7". */
export function formatPageNumbers(pages: number[]): string {
    const runs: { from: number; to: number }[] = [];

    for (const page of [...pages].sort((a, b) => a - b)) {
        const last = runs[runs.length - 1];

        if (last && last.to === page - 1) {
            last.to = page;
        } else {
            runs.push({ from: page, to: page });
        }
    }

    return formatPageRuns(runs);
}
