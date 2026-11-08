import { useEffect, useState } from 'react';

export type Geolocation =
    | { status: 'loading' }
    | { status: 'granted'; latitude: number; longitude: number }
    | { status: 'denied' };

/**
 * Asks the browser for the user's location once. The coordinates stay in memory only.
 */
export function useGeolocation(): Geolocation {
    const [state, setState] = useState<Geolocation>({ status: 'loading' });

    useEffect(() => {
        if (!('geolocation' in navigator)) {
            setState({ status: 'denied' });

            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) =>
                setState({
                    status: 'granted',
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                }),
            () => setState({ status: 'denied' }),
            { enableHighAccuracy: false, timeout: 8000, maximumAge: 300000 },
        );
    }, []);

    return state;
}
