import { ListChecks, MapPin, MessageCircle } from 'lucide-react';

const steps = [
    {
        icon: MapPin,
        title: 'Lihat yang buka dekat kamu',
        text: 'Daftar dan peta menunjukkan tempat yang masih buka, diurutkan dari jarak terdekat.',
    },
    {
        icon: ListChecks,
        title: 'Bandingkan harga',
        text: 'Tiap harga diberi tanda terverifikasi atau perkiraan, lengkap dengan tanggal pembaruannya.',
    },
    {
        icon: MessageCircle,
        title: 'Hubungi lewat WhatsApp',
        text: 'Tanya atau kirim pesanan langsung ke tempatnya. Website ini tidak mengurus pemesanan atau pembayaran.',
    },
];

/** Tiga langkah berurutan, jadi penomorannya memang menunjukkan urutan. */
export function HowItWorks() {
    return (
        <section aria-labelledby="cara-kerja" className="flex flex-col gap-6">
            <h2
                id="cara-kerja"
                className="font-display text-2xl font-bold tracking-tight sm:text-3xl"
            >
                Cara pakainya
            </h2>
            <ol className="grid gap-4 sm:grid-cols-3">
                {steps.map((step, index) => (
                    <li
                        key={step.title}
                        className="flex flex-col gap-3 rounded-lg border bg-card p-5"
                    >
                        <div className="flex items-center justify-between">
                            <span className="font-display text-4xl font-extrabold text-brand tabular-nums">
                                {index + 1}
                            </span>
                            <step.icon
                                className="size-6 text-muted-foreground"
                                aria-hidden
                            />
                        </div>
                        <h3 className="font-display text-lg font-semibold">
                            {step.title}
                        </h3>
                        <p className="text-sm text-muted-foreground">
                            {step.text}
                        </p>
                    </li>
                ))}
            </ol>
        </section>
    );
}
