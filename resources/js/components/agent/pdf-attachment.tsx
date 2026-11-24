import { FileText, Paperclip, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import type { PdfAttachmentState } from '@/hooks/use-pdf-attachment';
import { ColorCheckPanel } from '@/components/agent/color-check-panel';
import { formatNumber } from '@/lib/format';

/** Baris lampiran di form asisten: tombol pilih PDF, lalu kartu dengan jumlah halaman dan hasil cek warna. */
export function PdfAttachment({ pdf }: { pdf: PdfAttachmentState }) {
    const { file } = pdf;

    return (
        <div className="flex flex-col gap-1.5">
            <input
                ref={pdf.input}
                type="file"
                accept="application/pdf,.pdf"
                onChange={pdf.selectFile}
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

                    <ColorCheckPanel pdf={pdf} />
                </div>
            ) : (
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    onClick={pdf.browse}
                    className="min-h-11 self-start"
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
                Opsional. Browsermu mengukur warna tiap halaman, dan filenya
                tidak ikut dikirim ke server. Yang dikirim hanya persentase
                warna tiap halaman.
            </p>
        </div>
    );
}
