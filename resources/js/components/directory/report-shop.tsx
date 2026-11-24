import { Form, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import ReportController from '@/actions/App/Http/Controllers/ReportController';
import InputError from '@/components/input-error';
import { SectionHeading } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { login } from '@/routes';
import type { Auth } from '@/types';

export type ReportingOptions = {
    categories: { value: string; label: string }[];
    hasPending: boolean;
};

const MAX_LENGTH = 500;

/**
 * Bagian "Ada yang salah?" di halaman tempat. Tamu diajak masuk dulu, dan satu pengguna hanya punya satu laporan
 * yang menunggu per tempat.
 */
export function ReportShop({
    slug,
    reporting,
}: {
    slug: string;
    reporting: ReportingOptions;
}) {
    const { auth } = usePage<{ auth: Auth }>().props;
    const [open, setOpen] = useState(false);
    const [length, setLength] = useState(0);

    return (
        <section
            aria-labelledby="ada-yang-salah"
            className="flex flex-col items-start gap-3"
        >
            <SectionHeading id="ada-yang-salah">Ada yang salah?</SectionHeading>
            <p className="max-w-xl text-muted-foreground">
                Jam buka, nomor, atau harganya tidak cocok? Beri tahu kami, dan
                admin akan memeriksanya.
            </p>

            {!auth.user ? (
                <Button asChild variant="outline" className="min-h-11">
                    <Link href={login()}>Masuk untuk melapor</Link>
                </Button>
            ) : reporting.hasPending ? (
                <p className="rounded-sm bg-note-yellow px-3 py-2 text-sm font-medium">
                    Laporanmu untuk tempat ini sedang ditinjau admin.
                </p>
            ) : (
                <Dialog
                    open={open}
                    onOpenChange={(next) => {
                        setOpen(next);
                        setLength(0);
                    }}
                >
                    <DialogTrigger asChild>
                        <Button
                            variant="outline"
                            className="min-h-11"
                            data-test="report-shop-button"
                        >
                            Laporkan data salah
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogTitle>Laporkan data yang salah</DialogTitle>
                        <DialogDescription>
                            Pilih jenis masalahnya, lalu ceritakan apa yang
                            benar menurutmu.
                        </DialogDescription>

                        <Form
                            {...ReportController.store.form({ shop: slug })}
                            options={{ preserveScroll: true }}
                            onSuccess={() => setOpen(false)}
                            className="space-y-5"
                        >
                            {({ processing, errors }) => (
                                <>
                                    <fieldset className="grid gap-2">
                                        <legend className="text-sm font-medium">
                                            Jenis masalah
                                        </legend>
                                        <div className="flex flex-wrap gap-2">
                                            {reporting.categories.map(
                                                (category) => (
                                                    <label
                                                        key={category.value}
                                                        className="cursor-pointer"
                                                    >
                                                        <input
                                                            type="radio"
                                                            name="category"
                                                            value={
                                                                category.value
                                                            }
                                                            className="peer sr-only"
                                                        />
                                                        <span className="inline-flex min-h-11 items-center rounded-full border-2 border-foreground/15 bg-card px-4 text-sm font-semibold transition-colors peer-checked:border-primary peer-checked:bg-primary peer-checked:text-primary-foreground peer-focus-visible:ring-2 peer-focus-visible:ring-ring">
                                                            {category.label}
                                                        </span>
                                                    </label>
                                                ),
                                            )}
                                        </div>
                                        <InputError message={errors.category} />
                                    </fieldset>

                                    <div className="grid gap-2">
                                        <Label htmlFor="report-message">
                                            Pesan
                                        </Label>
                                        <textarea
                                            id="report-message"
                                            name="message"
                                            rows={4}
                                            maxLength={MAX_LENGTH}
                                            onChange={(event) =>
                                                setLength(
                                                    event.target.value.length,
                                                )
                                            }
                                            placeholder="Misalnya: hari Sabtu tutupnya jam 15.00"
                                            className="rounded-md border bg-card p-3 text-base outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                        />
                                        <p className="text-xs text-muted-foreground tabular-nums">
                                            {length} dari {MAX_LENGTH} karakter
                                        </p>
                                        <InputError message={errors.message} />
                                    </div>

                                    <DialogFooter className="gap-2">
                                        <DialogClose asChild>
                                            <Button
                                                variant="secondary"
                                                className="min-h-11"
                                            >
                                                Batal
                                            </Button>
                                        </DialogClose>
                                        <Button
                                            type="submit"
                                            className="min-h-11"
                                            disabled={processing}
                                            data-test="send-report-button"
                                        >
                                            Kirim laporan
                                        </Button>
                                    </DialogFooter>
                                </>
                            )}
                        </Form>
                    </DialogContent>
                </Dialog>
            )}
        </section>
    );
}
