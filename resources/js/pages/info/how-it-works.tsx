import { Head, Link } from '@inertiajs/react';
import { PageHeader, Section } from '@/components/info/page-header';
import { Button } from '@/components/ui/button';
import { index as directoryIndex } from '@/routes/directory';

const steps = [
    'Buka halaman Cari tempat. Izinkan lokasi kalau kamu mau daftar diurutkan dari yang terdekat.',
    'Lihat status buka, harga mulai dari, dan jarak tiap tempat. Pakai filter Masih buka kalau sedang buru-buru.',
    'Buka halaman sebuah tempat untuk melihat daftar harga lengkap, jam buka, layanan, dan lokasinya di peta.',
    'Hubungi tempatnya lewat tombol WhatsApp. Pemesanan dan pembayaran terjadi di antara kamu dan tempat itu, bukan di CariPrint.',
];

export default function HowItWorks() {
    return (
        <>
            <Head title="Cara kerja" />
            <main className="flex flex-col gap-10 px-4 py-8 sm:py-12">
                <PageHeader
                    title="Cara kerja CariPrint"
                    intro="CariPrint membantu kamu memilih tempat printing sebelum berangkat. Berikut apa yang terjadi di balik tiap halaman."
                />

                <Section title="Dari mencari sampai menghubungi">
                    <ol className="flex flex-col gap-3">
                        {steps.map((step, index) => (
                            <li key={step} className="flex gap-4">
                                <span className="font-display text-2xl leading-7 font-extrabold text-brand tabular-nums">
                                    {index + 1}
                                </span>
                                <span>{step}</span>
                            </li>
                        ))}
                    </ol>
                </Section>

                <Section title="Arti tanda yang kamu lihat">
                    <p>
                        <strong className="text-foreground">Buka</strong>,{' '}
                        <strong className="text-foreground">
                            Segera tutup
                        </strong>
                        , dan <strong className="text-foreground">Tutup</strong>{' '}
                        dihitung dari jam buka yang tercatat dan waktu sekarang
                        di Surabaya. Segera tutup berarti kurang dari 30 menit
                        sebelum tutup.
                    </p>
                    <p>
                        <strong className="text-foreground">
                            Terverifikasi
                        </strong>{' '}
                        berarti harga diambil dari survei tim CariPrint.{' '}
                        <strong className="text-foreground">Perkiraan</strong>{' '}
                        berarti belum disurvei, jadi bisa berbeda di lapangan.
                        Tanggal pembaruan tertera di tiap halaman tempat.
                    </p>
                    <p>
                        <strong className="text-foreground">Jarak</strong>{' '}
                        adalah garis lurus dari lokasimu ke tempat itu, bukan
                        jarak berkendara. Lokasimu hanya dipakai untuk
                        menghitung jarak dan tidak disimpan.
                    </p>
                </Section>

                <Section title="Apa yang dikerjakan AI">
                    <p>
                        Asisten cetak hanya membaca kalimatmu dan mengubahnya
                        menjadi spesifikasi: ukuran kertas, jumlah halaman
                        hitam-putih dan warna, jumlah rangkap, dan jilid.
                    </p>
                    <p>
                        Biaya, jarak, dan status buka tidak pernah dihitung oleh
                        AI. Semuanya dihitung dari data tempat dan daftar harga
                        yang tersimpan, dan hasilnya bisa kamu periksa sendiri.
                    </p>
                </Section>

                <div>
                    <Button asChild size="lg">
                        <Link href={directoryIndex().url}>
                            Mulai cari tempat
                        </Link>
                    </Button>
                </div>
            </main>
        </>
    );
}
