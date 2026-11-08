import { Head, router } from '@inertiajs/react';
import { useEffect } from 'react';
import { ShopList } from '@/components/directory/shop-list';
import { useGeolocation } from '@/hooks/use-geolocation';
import { nearby } from '@/routes/directory';
import type { ShopSummary } from '@/types';

export default function DirectoryIndex({ shops }: { shops: ShopSummary[] }) {
    const location = useGeolocation();

    useEffect(() => {
        if (location.status !== 'granted') {
            return;
        }

        router.post(
            nearby().url,
            { latitude: location.latitude, longitude: location.longitude },
            { preserveState: true, preserveScroll: true, only: ['shops'] },
        );
    }, [location]);

    const sorted = location.status === 'granted';

    return (
        <>
            <Head title="Cari tempat printing" />
            <main className="mx-auto flex w-full max-w-3xl flex-col gap-4 p-4">
                <div className="flex flex-wrap items-baseline justify-between gap-2">
                    <h1 className="text-xl font-semibold">
                        Tempat printing di sekitar CitraLand
                    </h1>
                    <p className="text-xs text-muted-foreground">
                        {sorted
                            ? 'Urut dari yang terdekat'
                            : 'Urut nama. Izinkan lokasi untuk urut jarak.'}
                    </p>
                </div>
                <ShopList shops={shops} />
            </main>
        </>
    );
}
