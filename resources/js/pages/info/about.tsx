import { Head, Link } from '@inertiajs/react';
import { Section } from '@/components/info/section';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { index as directoryIndex } from '@/routes/directory';
import { faq } from '@/routes/info';

export default function About() {
    return (
        <>
            <Head title="Tentang" />
            <main className="flex w-full flex-col gap-10 px-4 py-8 sm:py-12">
                <PageHeader
                    divided
                    title="Tentang CariPrint"
                    intro="Website untuk mahasiswa di sekitar CitraLand Surabaya yang ingin tahu tempat printing mana yang buka, berapa harganya, dan seberapa jauh."
                />

                <Section title="Kenapa dibuat">
                    <p>
                        Sebelum kelas, mahasiswa sering berkeliling dari satu
                        tempat printing ke tempat lain untuk mencari yang masih
                        buka dan harganya cocok. CariPrint mengumpulkan
                        informasi itu di satu tempat supaya kamu bisa memutuskan
                        dari HP sebelum berangkat.
                    </p>
                </Section>

                <Section title="Yang CariPrint lakukan dan tidak lakukan">
                    <p>
                        CariPrint hanya menghubungkan mahasiswa dengan tempat
                        printing. Tidak ada pemesanan dan tidak ada pembayaran
                        di website ini. Kamu menghubungi tempatnya sendiri lewat
                        WhatsApp.
                    </p>
                    <p>
                        Mencari tempat, membandingkan harga, dan memakai asisten
                        cetak bisa dilakukan tanpa login. Login dengan Google
                        hanya dibutuhkan kalau kamu ingin mengusulkan tempat
                        baru.
                    </p>
                </Section>

                <Section title="Status proyek">
                    <p>
                        CariPrint adalah proyek mahasiswa Universitas Ciputra
                        yang masih terus dikembangkan. Lokasi, alamat, dan jam
                        buka tempat diambil dari Google Maps. Harga masih
                        perkiraan sampai dikonfirmasi pemilik tempat, dan harga
                        yang sudah dikonfirmasi bertanda Terverifikasi.
                    </p>
                </Section>

                <Section title="Dibangun dengan">
                    <p>
                        Laravel dan React untuk aplikasinya, serta peta Leaflet
                        dengan gambar peta dari Esri.
                    </p>
                </Section>

                <div className="flex flex-wrap gap-3">
                    <Button asChild size="lg">
                        <Link href={directoryIndex().url}>Cari tempat</Link>
                    </Button>
                    <Button asChild size="lg" variant="outline">
                        <Link href={faq().url}>Pertanyaan umum</Link>
                    </Button>
                </div>
            </main>
        </>
    );
}
