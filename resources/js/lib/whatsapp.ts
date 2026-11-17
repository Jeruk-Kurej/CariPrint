/** Tautan chat WhatsApp ke sebuah nomor, dengan pesan awal kalau diisi. */
export function whatsappUrl(phone: string, message?: string): string {
    const query = message ? `?text=${encodeURIComponent(message)}` : '';

    return `https://wa.me/${phone}${query}`;
}
