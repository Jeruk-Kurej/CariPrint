import { Link } from '@inertiajs/react';
import { ChevronRight, MapPin, Scale, Sparkles } from 'lucide-react';
import { index as agentIndex } from '@/routes/agent';
import { index as compareIndex } from '@/routes/compare';
import { index as directoryIndex } from '@/routes/directory';

const features = [
    {
        icon: MapPin,
        title: 'Cari tempat',
        text: 'Daftar dan peta tempat printing di sekitar CitraLand, diurutkan dari yang terdekat atau termurah.',
        href: directoryIndex().url,
    },
    {
        icon: Scale,
        title: 'Bandingkan harga',
        text: 'Satu tabel yang menyejajarkan harga print, fotokopi, dan jilid di semua tempat. Yang termurah langsung terlihat.',
        href: compareIndex().url,
    },
    {
        icon: Sparkles,
        title: 'Asisten cetak',
        text: 'Tulis kebutuhanmu dengan kalimat biasa dan lihat estimasi biaya di tiap tempat.',
        href: agentIndex().url,
    },
];

/** Tiga pintu masuk ke fitur utama, masing-masing satu halaman sendiri. */
export function FeatureCards() {
    return (
        <section aria-labelledby="fitur" className="flex flex-col gap-6">
            <h2
                id="fitur"
                className="font-display text-2xl font-bold tracking-tight sm:text-3xl"
            >
                Yang bisa kamu lakukan
            </h2>
            <ul className="grid gap-4 md:grid-cols-3">
                {features.map((feature) => (
                    <li key={feature.title}>
                        <Link
                            href={feature.href}
                            className="group flex h-full flex-col gap-3 rounded-lg border bg-card p-5 transition-colors hover:border-primary"
                        >
                            <feature.icon
                                className="size-8 text-brand"
                                aria-hidden
                            />
                            <h3 className="font-display text-xl font-semibold">
                                {feature.title}
                            </h3>
                            <p className="flex-1 text-sm text-muted-foreground">
                                {feature.text}
                            </p>
                            <span className="inline-flex items-center gap-1 text-sm font-medium">
                                Buka
                                <ChevronRight
                                    className="size-4 transition-transform group-hover:translate-x-0.5"
                                    aria-hidden
                                />
                            </span>
                        </Link>
                    </li>
                ))}
            </ul>
        </section>
    );
}
