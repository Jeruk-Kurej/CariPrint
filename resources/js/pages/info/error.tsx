import { Head, Link } from '@inertiajs/react';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { homeUrl } from '@/lib/navigation';
import { index as directory } from '@/routes/directory';

const messages: Record<number, { title: string; intro: string }> = {
    403: {
        title: 'Kamu tidak punya akses',
        intro: 'Halaman ini hanya untuk pengguna tertentu. Kalau kamu merasa seharusnya bisa membukanya, coba masuk lagi.',
    },
    404: {
        title: 'Halaman tidak ditemukan',
        intro: 'Tautannya mungkin sudah lama, atau tempat yang kamu cari sudah tidak ada di CariPrint. Coba cari lagi dari daftar tempat.',
    },
    419: {
        title: 'Halaman sudah kedaluwarsa',
        intro: 'Halaman ini terlalu lama terbuka. Kembali ke beranda lalu coba lagi.',
    },
    500: {
        title: 'Ada yang salah di sisi kami',
        intro: 'Ini bukan salahmu. Coba lagi sebentar lagi. Kalau masih begini, kabari tim CariPrint.',
    },
    503: {
        title: 'CariPrint sedang dirawat',
        intro: 'Kami sedang memperbarui situs. Coba lagi beberapa menit lagi.',
    },
};

/** Halaman untuk kode error yang paling mungkin dilihat pengunjung, dengan jalan kembali yang jelas. */
export default function ErrorPage({ status }: { status: number }) {
    const message = messages[status] ?? messages[500];

    return (
        <>
            <Head title={message.title} />
            <main className="flex w-full flex-col gap-8 px-4 py-12 sm:py-20">
                <p className="font-display text-7xl font-extrabold tracking-tight text-primary tabular-nums sm:text-8xl">
                    {status}
                </p>
                <PageHeader title={message.title} intro={message.intro} />
                <div className="flex flex-wrap gap-3">
                    <Button asChild size="lg" className="min-h-11">
                        <Link href={homeUrl}>Ke beranda</Link>
                    </Button>
                    <Button
                        asChild
                        size="lg"
                        variant="outline"
                        className="min-h-11"
                    >
                        <Link href={directory().url}>Lihat semua tempat</Link>
                    </Button>
                </div>
            </main>
        </>
    );
}
