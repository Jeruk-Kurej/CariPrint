export type PageItem = number | 'gap';

/** Nomor halaman yang ditampilkan: pertama, terakhir, dan yang di sekitar halaman aktif. Sisanya diganti titik-titik. */
export function pageItems(current: number, total: number): PageItem[] {
    const wanted = new Set(
        [1, total, current - 1, current, current + 1].filter(
            (page) => page >= 1 && page <= total,
        ),
    );
    const pages = [...wanted].sort((a, b) => a - b);

    return pages.flatMap((page, index) =>
        index > 0 && page - pages[index - 1] > 1
            ? (['gap', page] as PageItem[])
            : [page],
    );
}
