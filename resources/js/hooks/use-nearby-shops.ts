import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { useGeolocation } from '@/hooks/use-geolocation';
import { nearby } from '@/routes/directory';

/**
 * Meminta lokasi, lalu meminta server mengurutkan tempat dari yang terdekat.
 * Koordinat dikirim lewat badan permintaan dan alamat halaman tidak berubah.
 */
export function useNearbyShops() {
    const location = useGeolocation();
    const [sorting, setSorting] = useState(false);
    const hasDistance = location.status === 'granted';

    useEffect(() => {
        if (location.status !== 'granted') {
            return;
        }

        setSorting(true);
        router.post(
            nearby().url,
            { latitude: location.latitude, longitude: location.longitude },
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ['shops'],
                onFinish: () => setSorting(false),
            },
        );
    }, [location]);

    return { hasDistance, sorting };
}
