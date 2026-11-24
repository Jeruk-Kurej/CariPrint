import { Form, Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import { Fastener } from '@/components/directory/flyer-parts';
import InputError from '@/components/input-error';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { homeUrl } from '@/lib/navigation';
import { store } from '@/routes/contact';

const fieldClass = 'flex flex-col gap-1.5';
const MAX_LENGTH = 1000;

/**
 * Formulir Hubungi kami. Pesan masuk ke panel admin dan ke email tim, lalu dibalas lewat email. Tamu mengisi
 * emailnya sendiri, sedangkan yang sudah masuk memakai email akunnya.
 */
export default function Contact({
    categories,
    email,
    sent,
}: {
    categories: { value: string; label: string }[];
    email: string | null;
    sent: boolean;
}) {
    const [length, setLength] = useState(0);

    return (
        <>
            <Head title="Hubungi kami" />
            <main className="flex flex-col gap-8 px-4 py-8 sm:py-12">
                <PageHeader
                    title="Hubungi kami"
                    intro="Ada kendala dengan akunmu atau datamu, atau mau kasih saran? Tulis di sini, nanti kami balas lewat email."
                />

                {sent ? (
                    <div className="relative flex max-w-2xl flex-col items-start gap-4 rounded-sm bg-note-mint p-5 paper-shadow sm:p-6">
                        <Fastener id={3} />
                        <h2 className="font-display text-2xl font-extrabold">
                            Terima kasih, pesanmu sudah kami terima
                        </h2>
                        <p>Kami membalas lewat email yang kamu tulis.</p>
                        <Button asChild className="min-h-11">
                            <Link href={homeUrl}>Kembali ke beranda</Link>
                        </Button>
                    </div>
                ) : (
                    <Form
                        {...store.form()}
                        onSuccess={() => setLength(0)}
                        className="relative flex max-w-2xl flex-col gap-4 rounded-sm bg-note-sky p-5 paper-shadow sm:p-6"
                    >
                        {({ processing, errors }) => (
                            <>
                                <Fastener id={1} />

                                <fieldset className="flex flex-col gap-2">
                                    <legend className="text-sm font-medium">
                                        Jenis pesan
                                    </legend>
                                    <div className="flex flex-wrap gap-2">
                                        {categories.map((category) => (
                                            <label
                                                key={category.value}
                                                className="cursor-pointer"
                                            >
                                                <input
                                                    type="radio"
                                                    name="category"
                                                    value={category.value}
                                                    className="peer sr-only"
                                                />
                                                <span className="inline-flex min-h-11 items-center rounded-full border-2 border-foreground/15 bg-card px-4 text-sm font-semibold transition-colors peer-checked:border-primary peer-checked:bg-primary peer-checked:text-primary-foreground peer-focus-visible:ring-2 peer-focus-visible:ring-ring">
                                                    {category.label}
                                                </span>
                                            </label>
                                        ))}
                                    </div>
                                    <InputError message={errors.category} />
                                </fieldset>

                                {email !== null ? (
                                    <p className="text-sm text-foreground/70">
                                        Dikirim sebagai{' '}
                                        <span className="font-semibold text-foreground">
                                            {email}
                                        </span>
                                        . Balasan kami dikirim ke email ini.
                                    </p>
                                ) : (
                                    <div className={fieldClass}>
                                        <Label htmlFor="email">Emailmu</Label>
                                        <Input
                                            id="email"
                                            name="email"
                                            type="email"
                                            required
                                            maxLength={255}
                                            autoComplete="email"
                                            className="min-h-11 bg-card"
                                            placeholder="nama@email.com"
                                        />
                                        <p className="text-xs text-foreground/70">
                                            Hanya dipakai untuk membalas
                                            pesanmu.
                                        </p>
                                        <InputError message={errors.email} />
                                    </div>
                                )}

                                <div className={fieldClass}>
                                    <Label htmlFor="message">Pesan</Label>
                                    <textarea
                                        id="message"
                                        name="message"
                                        rows={5}
                                        maxLength={MAX_LENGTH}
                                        onChange={(event) =>
                                            setLength(event.target.value.length)
                                        }
                                        className="rounded-md border bg-card p-3 text-base outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                        placeholder="Misalnya: aku mau akunku dihapus tapi tombolnya tidak bisa dipakai"
                                    />
                                    <p className="text-xs text-foreground/70 tabular-nums">
                                        {length} dari {MAX_LENGTH} karakter
                                    </p>
                                    <InputError message={errors.message} />
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
                                    className="min-h-11"
                                    disabled={processing}
                                >
                                    {processing && <Spinner />}
                                    {processing
                                        ? 'Mengirim pesan'
                                        : 'Kirim pesan'}
                                </Button>
                                <p className="text-sm text-foreground/70">
                                    Pesanmu hanya dibaca admin CariPrint.
                                </p>
                            </>
                        )}
                    </Form>
                )}
            </main>
        </>
    );
}
