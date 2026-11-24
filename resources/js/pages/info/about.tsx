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
                        informasinya di satu website, jadi kamu bisa memutuskan
                        dari HP sebelum berangkat.
                    </p>
                </Section>

                <Section title="Yang bisa dan tidak bisa dilakukan CariPrint">
                    <p>
                        CariPrint hanya mempertemukan kamu dengan tempat print.
                        Pesan dan bayarnya langsung ke tempatnya lewat WhatsApp.
                    </p>
                    <p>
                        Kamu bisa mencari tempat, membandingkan harga, dan
                        memakai asisten cetak tanpa login. Login dengan Google
                        hanya perlu kalau kamu mau mengusulkan tempat baru.
                    </p>
                </Section>

                <Section title="Status proyek">
                    <p>
                        CariPrint adalah proyek mahasiswa Universitas Ciputra
                        dan masih terus dikembangkan. Lokasi, alamat, dan jam
                        buka tempat berasal dari Google Maps. Harga yang belum
                        dikonfirmasi pemilik tempat masih berupa perkiraan, dan
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
