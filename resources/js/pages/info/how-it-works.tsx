import { Head, Link } from '@inertiajs/react';
import { Fastener } from '@/components/directory/flyer-parts';
import { Section } from '@/components/info/section';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { index as agentIndex } from '@/routes/agent';
import { index as directoryIndex } from '@/routes/directory';

const steps = [
    'Buka halaman Cari tempat. Izinkan lokasi kalau kamu mau daftar diurutkan dari yang terdekat.',
    'Lihat status buka, harga mulai dari, dan jarak tiap tempat. Pakai filter Masih buka kalau sedang buru-buru.',
    'Buka halaman sebuah tempat untuk melihat daftar harga lengkap, jam buka, layanan, dan lokasinya di peta.',
    'Hubungi tempatnya lewat tombol WhatsApp. Pesan dan bayarnya langsung ke tempat itu.',
];

export default function HowItWorks() {
    return (
        <>
            <Head title="Cara kerja" />
            <main className="flex w-full flex-col gap-10 px-4 py-8 sm:py-12">
                <PageHeader
                    divided
                    title="Cara kerja CariPrint"
                    intro="CariPrint membantu kamu memilih tempat print sebelum berangkat."
                />

                <section className="flex flex-col gap-4">
                    <h2 className="font-display text-2xl font-bold tracking-tight">
                        Dari mencari sampai menghubungi
                    </h2>
                    <ol className="grid gap-4 sm:grid-cols-2">
                        {steps.map((step, index) => (
                            <li
                                key={step}
                                className="flex gap-4 rounded-md border bg-card p-4"
                            >
                                <span className="font-display text-2xl leading-7 font-extrabold text-brand tabular-nums">
                                    {index + 1}
                                </span>
                                <span className="text-muted-foreground">
                                    {step}
                                </span>
                            </li>
                        ))}
                    </ol>
                </section>

                <div className="grid items-start gap-8 lg:grid-cols-[1.2fr_1fr]">
                    <Section title="Arti tanda yang kamu lihat">
                        <p>
                            <strong className="text-foreground">Buka</strong>,{' '}
                            <strong className="text-foreground">
                                Segera tutup
                            </strong>
                            , dan{' '}
                            <strong className="text-foreground">Tutup</strong>{' '}
                            dihitung dari jam buka yang tercatat dan waktu
                            sekarang di Surabaya. Segera tutup berarti kurang
                            dari 30 menit sebelum tutup.
                        </p>
                        <p>
                            <strong className="text-foreground">
                                Terverifikasi
                            </strong>{' '}
                            berarti harganya sudah dikonfirmasi langsung ke
                            tempatnya.{' '}
                            <strong className="text-foreground">
                                Perkiraan
                            </strong>{' '}
                            berarti belum dikonfirmasi, jadi bisa berbeda di
                            lapangan. Tanggal pembaruannya ada di halaman tiap
                            tempat.
                        </p>
                        <p>
                            <strong className="text-foreground">Jarak</strong>{' '}
                            adalah garis lurus dari lokasimu ke tempatnya, jadi
                            rute sebenarnya bisa lebih jauh. Lokasimu hanya
                            dipakai untuk menghitung jarak dan tidak disimpan.
                        </p>
                    </Section>

                    <aside
                        className={`relative flex flex-col gap-3 rounded-sm bg-note-yellow p-5 paper-shadow sm:p-6`}
                    >
                        <Fastener id={1} />
                        <h2 className="font-display text-2xl font-bold tracking-tight">
                            Apa yang dikerjakan AI
                        </h2>
                        <p className="text-sm text-foreground/85">
                            Asisten cetak hanya membaca kalimatmu dan
                            mengubahnya menjadi spesifikasi: ukuran kertas,
                            jumlah halaman hitam-putih dan warna, jumlah
                            rangkap, dan jilid. Kalau kamu melampirkan PDF,
                            browsermu mengukur warna tiap halaman, lalu aturan
                            di server (bukan AI) menilai halaman mana yang perlu
                            warna dan berapa hematnya. Filenya tidak diunggah ke
                            server.
                        </p>
                        <p className="text-sm text-foreground/85">
                            AI tidak pernah menghitung biaya, jarak, atau status
                            buka. Semuanya dihitung dari data tempat dan daftar
                            harga yang tersimpan.
                        </p>
                        <div className="flex flex-wrap gap-2 pt-2">
                            <Button asChild>
                                <Link href={directoryIndex().url}>
                                    Mulai cari tempat
                                </Link>
                            </Button>
                            <Button asChild variant="outline">
                                <Link href={agentIndex().url}>
                                    Coba asisten cetak
                                </Link>
                            </Button>
                        </div>
                    </aside>
                </div>
            </main>
        </>
    );
}
