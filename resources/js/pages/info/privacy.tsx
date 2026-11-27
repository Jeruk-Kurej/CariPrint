import { Head, Link } from '@inertiajs/react';
import { Section } from '@/components/info/section';
import { PageHeader } from '@/components/page-header';
import { contactUrl } from '@/lib/navigation';

/** Kebijakan privasi. Isinya harus selalu cocok dengan data yang benar-benar diproses aplikasi. */
export default function Privacy() {
    return (
        <>
            <Head title="Kebijakan privasi" />
            <main className="flex w-full flex-col gap-10 px-4 py-8 sm:py-12">
                <PageHeader
                    divided
                    title="Kebijakan privasi"
                    intro="CariPrint tidak menjual datamu dan tidak menyimpan lokasimu. Di bawah ini yang kami proses, untuk apa, dan siapa yang bisa melihatnya."
                />

                <Section title="Data yang kami simpan">
                    <p>
                        Kalau kamu masuk dengan Google, kami menyimpan nama,
                        alamat email, dan ID akun Google-mu. Datanya dipakai
                        untuk mengenali akunmu dan mengabari usulan tempat yang
                        kamu kirim. Kami tidak menerima kata sandi Google-mu dan
                        tidak membaca Gmail, kontak, atau data Google lain.
                    </p>
                    <p>
                        Kalau kamu mengusulkan tempat, kami menyimpan nama
                        tempat, alamat, tautan Google Maps, catatan yang kamu
                        tulis, dan emailmu. Hanya admin CariPrint yang
                        melihatnya, dan usulan itu tidak tampil di website
                        sebelum admin menambahkannya.
                    </p>
                    <p>
                        Agar kamu tetap masuk, sistem menyimpan sesi sementara,
                        termasuk alamat IP dan jenis browser. Pembatas jumlah
                        kiriman juga memakai alamat IP untuk sementara waktu.
                    </p>
                </Section>

                <Section title="Lokasimu">
                    <p>
                        Browser meminta izinmu sebelum membagikan lokasi.
                        Koordinatnya dikirim ke server hanya untuk menghitung
                        jarak garis lurus ke tempat printing saat itu juga, dan{' '}
                        <strong className="text-foreground">
                            tidak disimpan atau dicatat
                        </strong>
                        . Kalau kamu menolak, CariPrint tetap bisa dipakai dan
                        daftar tempat diurutkan berdasarkan nama.
                    </p>
                </Section>

                <Section title="Asisten cetak">
                    <p>
                        Kalimat yang kamu tulis dikirim ke Google Gemini supaya
                        diubah menjadi spesifikasi cetak. Jangan menulis data
                        pribadi di kolom itu.
                    </p>
                    <p>
                        File PDF yang kamu lampirkan dibaca di browsermu dan{' '}
                        <strong className="text-foreground">
                            tidak diunggah
                        </strong>
                        . Server hanya menerima jumlah halaman dan berapa persen
                        tiap halaman berwarna, tidak pernah isi halamannya.
                    </p>
                </Section>

                <Section title="Cari pakai AI">
                    <p>
                        Kalau kamu menekan tombol Cari pakai AI, isi kotak
                        pencarian dikirim ke Google Gemini supaya diubah menjadi
                        filter. Lokasimu tidak ikut dikirim, dan fitur ini tidak
                        menyimpan kalimatmu di database. Teks di kotak pencarian
                        juga tertulis di alamat halaman, jadi jangan menulis
                        data pribadi di situ.
                    </p>
                </Section>

                <Section title="Laporan data salah">
                    <p>
                        Kalau kamu melaporkan data tempat yang keliru, kami
                        menyimpan nama tempatnya, jenis masalah, pesanmu, dan
                        akunmu. Hanya admin CariPrint yang melihatnya, dan kamu
                        bisa melihat hasilnya di halaman Akunku.
                    </p>
                </Section>

                <Section title="Email">
                    <p>
                        Kami mengirim email tanda terima saat usulanmu masuk dan
                        email hasil saat admin menerima atau menolaknya. Isinya
                        hanya tentang usulanmu. Laporan data salah tidak
                        dikirimi email ke kamu, hanya ke admin.
                    </p>
                </Section>

                <Section title="Siapa yang memproses datamu">
                    <p>
                        Data tidak dijual dan tidak dipakai untuk iklan. Pihak
                        lain yang ikut memproses hanya Google (untuk login dan
                        asisten cetak, dan Cari pakai AI), layanan pengiriman
                        email, dan server tempat CariPrint berjalan.
                    </p>
                </Section>

                <Section title="Menghapus datamu">
                    <p>
                        Kamu bisa menghapus akun dan usulan tempatmu kapan saja
                        lewat halaman Akunku. Datanya langsung terhapus permanen
                        dan tidak bisa dikembalikan.
                    </p>
                    <p>
                        Kalau ada kendala, tulis lewat{' '}
                        <Link
                            href={contactUrl}
                            className="font-medium text-primary underline underline-offset-2"
                        >
                            formulir Hubungi kami
                        </Link>
                        . Kami membalas lewat email.
                    </p>
                </Section>

                <Section title="Pesan lewat Hubungi kami">
                    <p>
                        Kalau kamu menulis lewat formulir Hubungi kami, kami
                        menyimpan emailmu, jenis pesan, dan isi pesanmu. Hanya
                        admin CariPrint yang melihatnya, dan pesan dari emailmu
                        ikut terhapus saat kamu menghapus akun.
                    </p>
                </Section>

                <p className="text-sm text-muted-foreground">
                    Terakhir diperbarui 27 November 2026.
                </p>
            </main>
        </>
    );
}
