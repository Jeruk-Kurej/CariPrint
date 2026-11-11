import { BadgeCheck, MapPinOff, MessageCircle } from 'lucide-react';

const points = [
    {
        icon: MapPinOff,
        title: 'Lokasimu tidak disimpan',
        text: 'Dipakai sebentar untuk menghitung jarak, lalu dibuang.',
    },
    {
        icon: BadgeCheck,
        title: 'Harga ada sumbernya',
        text: 'Tiap harga bertanda terverifikasi atau perkiraan, lengkap dengan tanggal pembaruan.',
    },
    {
        icon: MessageCircle,
        title: 'Langsung ke WhatsApp',
        text: 'Tidak ada pemesanan atau pembayaran di sini. Kamu menghubungi tempatnya sendiri.',
    },
];

export function TrustStrip() {
    return (
        <ul className="grid gap-6 border-y py-8 sm:grid-cols-3">
            {points.map((point) => (
                <li key={point.title} className="flex gap-3">
                    <point.icon
                        className="mt-0.5 size-6 shrink-0 text-brand"
                        aria-hidden
                    />
                    <div>
                        <p className="font-display font-semibold">
                            {point.title}
                        </p>
                        <p className="text-sm text-muted-foreground">
                            {point.text}
                        </p>
                    </div>
                </li>
            ))}
        </ul>
    );
}
