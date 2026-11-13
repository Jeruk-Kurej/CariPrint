/**
 * Menghitung jumlah halaman PDF di perangkat pengguna. Filenya tidak dikirim ke mana pun.
 *
 * pdf.js baru dimuat saat fungsi ini dipanggil supaya halaman lain tetap ringan. Yang dipakai build
 * "legacy" karena build biasanya hanya berjalan di browser versi terbaru. Modul worker diimpor langsung
 * sehingga pdf.js membaca file di thread utama: untuk sekadar menghitung halaman ini cukup cepat,
 * dan tidak bergantung pada dukungan worker di browser maupun pengaturan server.
 */
export async function countPdfPages(file: File): Promise<number> {
    const [pdfjs, data] = await Promise.all([
        import('pdfjs-dist/legacy/build/pdf.mjs'),
        file.arrayBuffer(),
        import('pdfjs-dist/legacy/build/pdf.worker.min.mjs'),
    ]);

    const loading = pdfjs.getDocument({ data });

    try {
        const pdf = await loading.promise;

        return pdf.numPages;
    } finally {
        await loading.destroy();
    }
}
