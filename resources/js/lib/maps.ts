/**
 * Tautan rute ke sebuah tempat di Google Maps. Hanya tujuan yang ditulis: posisi pengguna dibaca sendiri
 * oleh aplikasi Maps di perangkatnya, jadi lokasi pengguna tidak pernah lewat server CariPrint.
 */
export function directionsUrl(latitude: number, longitude: number): string {
    return `https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`;
}
