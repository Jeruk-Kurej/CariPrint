import { FileText, Paperclip, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import type { PdfAttachmentState } from '@/hooks/use-pdf-attachment';
import { formatNumber, formatPageNumbers } from '@/lib/format';

/** Baris lampiran di form asisten: tombol pilih PDF, lalu kartu ringkasan jumlah halaman warna dan hitam-putih. */
export function PdfAttachment({ pdf }: { pdf: PdfAttachmentState }) {
    const { file } = pdf;
    const bwPages =
        file.status === 'ready' ? file.pages - file.coloredPages.length : 0;

    return (
        <div className="flex flex-col gap-1.5">
            <input
                ref={pdf.input}
                type="file"
                accept="application/pdf,.pdf"
                onChange={pdf.choose}
                className="hidden"
            />

            {file.status === 'reading' || file.status === 'ready' ? (
                <div className="flex w-full flex-col gap-2.5 rounded-md border bg-card p-3 text-sm">
                    <div className="flex items-center gap-2">
                        {file.status === 'reading' ? (
                            <Spinner className="shrink-0" />
                        ) : (
                            <FileText
                                className="size-4 shrink-0 text-primary"
                                aria-hidden
                            />
                        )}
                        <span className="min-w-0 flex-1 truncate font-medium">
                            {file.name}
                        </span>
                        <span className="shrink-0 text-muted-foreground tabular-nums">
                            {file.status === 'ready'
                                ? `${formatNumber(file.pages)} halaman`
                                : file.total === null
                                  ? 'Membaca file'
                                  : `Memeriksa warna ${formatNumber(file.done)}/${formatNumber(file.total)}`}
                        </span>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            onClick={pdf.remove}
                            className="-my-1 -mr-1 size-7"
                            aria-label="Hapus lampiran"
                        >
                            <X />
                        </Button>
                    </div>

                    {file.status === 'ready' && (
                        <div className="flex flex-wrap gap-2 border-t pt-2.5">
                            {file.coloredPages.length === 0 ? (
                                <span className="rounded-md bg-secondary px-2.5 py-1 text-xs font-medium">
                                    Semua {formatNumber(file.pages)} halaman
                                    hitam-putih
                                </span>
                            ) : (
                                <>
                                    <span className="rounded-md bg-note-pink px-2.5 py-1 text-xs font-semibold text-foreground">
                                        {formatNumber(file.coloredPages.length)}{' '}
                                        halaman berwarna (hal.{' '}
                                        {formatPageNumbers(file.coloredPages)})
                                    </span>
                                    {bwPages > 0 && (
                                        <span className="rounded-md bg-secondary px-2.5 py-1 text-xs font-medium text-foreground">
                                            {formatNumber(bwPages)} halaman
                                            hitam-putih
                                        </span>
                                    )}
                                </>
                            )}
                        </div>
                    )}
                </div>
            ) : (
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    onClick={pdf.browse}
                    className="self-start"
                >
                    <Paperclip />
                    Lampirkan PDF
                </Button>
            )}

            {file.status === 'rejected' && (
                <p role="alert" className="text-sm text-destructive">
                    {file.message}
                </p>
            )}

            <p className="text-xs text-muted-foreground">
                Opsional. File PDF diperiksa di perangkatmu untuk menghitung
                halaman warna & hitam-putih, dan tidak diunggah ke server.
            </p>
        </div>
    );
}
