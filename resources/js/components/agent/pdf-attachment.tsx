import { FileText, Paperclip, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import type { PdfAttachmentState } from '@/hooks/use-pdf-attachment';
import { formatNumber, formatPageNumbers } from '@/lib/format';

/** Baris lampiran di form asisten: tombol pilih PDF, lalu nama file dan jumlah halamannya. */
export function PdfAttachment({ pdf }: { pdf: PdfAttachmentState }) {
    const { file } = pdf;

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
                <div className="flex w-full max-w-md items-center gap-2 rounded-md border bg-card py-1 pr-1 pl-3 text-sm">
                    {file.status === 'reading' ? (
                        <Spinner className="shrink-0" />
                    ) : (
                        <FileText
                            className="size-4 shrink-0 text-primary"
                            aria-hidden
                        />
                    )}
                    <span className="min-w-0 flex-1 truncate">{file.name}</span>
                    <span className="shrink-0 font-medium tabular-nums">
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
                        aria-label="Hapus lampiran"
                    >
                        <X />
                    </Button>
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

            {file.status === 'ready' && (
                <p className="text-sm">
                    {file.coloredPages.length === 0 ? (
                        'Tidak ada halaman berwarna, semuanya bisa hitam-putih.'
                    ) : (
                        <>
                            <span className="font-medium">
                                {formatNumber(file.coloredPages.length)} halaman
                                berwarna:
                            </span>{' '}
                            {formatPageNumbers(file.coloredPages)}
                        </>
                    )}
                </p>
            )}

            {file.status === 'rejected' && (
                <p role="alert" className="text-sm text-destructive">
                    {file.message}
                </p>
            )}

            <p className="text-sm text-muted-foreground">
                Opsional. Dengan PDF terlampir, kamu bisa menulis{' '}
                <span className="font-medium text-foreground">
                    print semua kecuali halaman terakhir
                </span>{' '}
                atau{' '}
                <span className="font-medium text-foreground">
                    hitam putih, warna hanya yang perlu
                </span>
                . File dibaca di perangkatmu dan tidak diunggah.
            </p>
        </div>
    );
}
