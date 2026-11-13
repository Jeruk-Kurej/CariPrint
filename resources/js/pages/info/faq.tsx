import { Head } from '@inertiajs/react';
import { FaqItem } from '@/components/info/faq-item';
import { PageHeader } from '@/components/info/page-header';

const questions: { question: string; answer: string }[] = [
    {
        question: 'Apakah saya bisa memesan atau membayar lewat CariPrint?',
        answer: 'Tidak. CariPrint hanya menampilkan informasi tempat printing. Pemesanan dan pembayaran terjadi langsung antara kamu dan tempatnya, biasanya lewat WhatsApp.',
    },
    {
        question: 'Apakah CariPrint menyimpan lokasi saya?',
        answer: 'Tidak. Lokasimu hanya dipakai saat itu juga untuk menghitung jarak ke tiap tempat, dan tidak disimpan maupun dicatat.',
    },
    {
        question: 'Apa bedanya harga Terverifikasi dan Perkiraan?',
        answer: 'Terverifikasi berarti harga diambil dari survei langsung tim CariPrint. Perkiraan berarti belum disurvei, jadi bisa berbeda saat kamu cek ke tempatnya. Tanggal pembaruan tertera di halaman tiap tempat.',
    },
    {
        question: 'Bagaimana status Buka dan Tutup ditentukan?',
        answer: 'Dihitung dari jam buka yang tercatat untuk tiap tempat dan waktu sekarang di Surabaya. Kalau ada tempat yang libur mendadak, informasinya bisa belum terkini, jadi hubungi dulu lewat WhatsApp.',
    },
    {
        question: 'Jarak yang ditampilkan itu jarak berkendara?',
        answer: 'Bukan. Itu jarak garis lurus dari lokasimu ke tempatnya, jadi rute sebenarnya bisa sedikit lebih jauh.',
    },
    {
        question: 'Apakah AI yang menentukan harga?',
        answer: 'Tidak. AI hanya membaca permintaanmu menjadi spesifikasi cetak. Biaya selalu dihitung dari daftar harga tiap tempat.',
    },
    {
        question: 'Dari mana data tempatnya?',
        answer: 'Dari survei langsung tim CariPrint ke tiap tempat. Selama survei berjalan, sebagian data di website ini masih contoh dan ditandai sebagai contoh.',
    },
    {
        question: 'Apakah memakai CariPrint berbayar?',
        answer: 'Tidak. Semua fitur bisa dipakai gratis.',
    },
];

export default function Faq() {
    return (
        <>
            <Head title="Pertanyaan umum" />
            <main className="mx-auto flex w-full max-w-5xl flex-col gap-8 px-4 py-8 sm:py-12">
                <PageHeader
                    title="Pertanyaan umum"
                    intro="Jawaban singkat untuk hal yang paling sering ditanyakan tentang CariPrint."
                />
                <div className="max-w-3xl border-t">
                    {questions.map((item) => (
                        <FaqItem key={item.question} question={item.question}>
                            <p>{item.answer}</p>
                        </FaqItem>
                    ))}
                </div>
            </main>
        </>
    );
}
