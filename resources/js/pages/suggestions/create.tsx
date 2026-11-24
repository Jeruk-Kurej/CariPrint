import { Form, Head, Link } from '@inertiajs/react';
import { Fastener } from '@/components/directory/flyer-parts';
import InputError from '@/components/input-error';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { index as directory } from '@/routes/directory';
import { create, store } from '@/routes/suggestions';

const fieldClass = 'flex flex-col gap-1.5';

/** Formulir usul tempat baru. Usulan dikirim ke admin dan baru tampil setelah admin menambahkannya. */
export default function SuggestionCreate({
    submitted,
    email,
}: {
    submitted: boolean;
    email: string | null;
}) {
    return (
        <>
            <Head title="Usulkan tempat" />
            <main className="flex flex-col gap-8 px-4 py-8 sm:py-12">
                <PageHeader
                    title="Usulkan tempat printing"
                    intro="Tahu tempat print yang belum ada di CariPrint? Kirim datanya. Tim kami mengecek dulu, lalu menambahkannya ke daftar."
                />

                {email === null ? (
                    <section className="relative flex max-w-2xl flex-col items-start gap-3 rounded-sm bg-note-sky p-6 paper-shadow">
                        <Fastener id={1} />
                        <h2 className="font-display text-2xl font-extrabold">
                            Masuk dulu untuk mengusulkan
                        </h2>
                        <p>
                            Kami minta kamu masuk dengan Google supaya tanda
                            terima dan hasil usulan bisa dikirim ke emailmu, dan
                            kamu bisa melihat statusnya di halaman Akunku.
                            Setelah masuk, kamu kembali ke halaman ini.
                        </p>
                        <Button asChild size="lg">
                            <Link href={login()}>Masuk untuk mengusulkan</Link>
                        </Button>
                    </section>
                ) : submitted ? (
                    <section
                        role="status"
                        className="relative flex max-w-2xl flex-col items-start gap-3 rounded-sm bg-note-mint p-6 paper-shadow"
                    >
                        <Fastener id={3} />
                        <h2 className="font-display text-2xl font-extrabold">
                            Terima kasih, usulanmu sudah kami terima
                        </h2>
                        <p>
                            Kami mengirim tanda terima ke emailmu. Admin
                            mengecek datanya dulu, lalu memberi kabar lewat
                            email yang sama. Tempat itu muncul di daftar setelah
                            ditambahkan.
                        </p>
                        <div className="flex flex-wrap gap-2">
                            <Button asChild>
                                <Link href={directory().url}>
                                    Lihat semua tempat
                                </Link>
                            </Button>
                            <Button asChild variant="outline">
                                <Link href={create().url}>Usulkan lagi</Link>
                            </Button>
                        </div>
                    </section>
                ) : (
                    <Form
                        {...store.form()}
                        className="relative flex max-w-2xl flex-col gap-4 rounded-sm bg-note-sky p-5 paper-shadow sm:p-6"
                    >
                        {({ processing, errors }) => (
                            <>
                                <Fastener id={1} />
                                <p className="text-sm text-foreground/70">
                                    Dikirim sebagai{' '}
                                    <span className="font-semibold text-foreground">
                                        {email}
                                    </span>
                                    . Tanda terima dan hasil usulannya kami
                                    kirim ke email ini.
                                </p>
                                <div className={fieldClass}>
                                    <Label htmlFor="name">Nama tempat</Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        required
                                        maxLength={120}
                                        className="bg-card"
                                        placeholder="Misalnya: Print Sejahtera"
                                    />
                                    <InputError message={errors.name} />
                                </div>
                                <div className={fieldClass}>
                                    <Label htmlFor="address">Alamat</Label>
                                    <Input
                                        id="address"
                                        name="address"
                                        required
                                        maxLength={255}
                                        className="bg-card"
                                        placeholder="Nama jalan atau ruko, dan patokannya"
                                    />
                                    <InputError message={errors.address} />
                                </div>
                                <div className={fieldClass}>
                                    <Label htmlFor="maps_url">
                                        Tautan Google Maps (opsional)
                                    </Label>
                                    <Input
                                        id="maps_url"
                                        name="maps_url"
                                        type="url"
                                        maxLength={500}
                                        className="bg-card"
                                        placeholder="https://maps.app.goo.gl/..."
                                    />
                                    <InputError message={errors.maps_url} />
                                </div>
                                <div className={fieldClass}>
                                    <Label htmlFor="note">
                                        Catatan (opsional)
                                    </Label>
                                    <textarea
                                        id="note"
                                        name="note"
                                        rows={3}
                                        maxLength={500}
                                        className="rounded-md border bg-card p-3 text-base outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                        placeholder="Misalnya: jam buka, layanan, atau harga yang kamu tahu"
                                    />
                                    <InputError message={errors.note} />
                                </div>
                                <div aria-hidden className="hidden">
                                    <label htmlFor="website">Website</label>
                                    <input
                                        id="website"
                                        name="website"
                                        tabIndex={-1}
                                        autoComplete="off"
                                    />
                                </div>
                                <Button
                                    type="submit"
                                    size="lg"
                                    disabled={processing}
                                >
                                    {processing && <Spinner />}
                                    {processing
                                        ? 'Mengirim usulan'
                                        : 'Kirim usulan'}
                                </Button>
                                <p className="text-sm text-foreground/70">
                                    Usulan tidak langsung tampil di website.
                                    Lokasimu tidak ikut dikirim.
                                </p>
                            </>
                        )}
                    </Form>
                )}
            </main>
        </>
    );
}
