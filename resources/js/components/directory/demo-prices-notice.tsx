/** Shown wherever prices come from the demo seeder, so nobody mistakes sample numbers for a real price list. */
export function DemoPricesNotice() {
    return (
        <p
            role="note"
            className="rounded-md bg-note-yellow px-3 py-2 text-sm font-medium"
        >
            Harga di halaman ini adalah data contoh untuk keperluan demo, bukan
            harga resmi dari tempatnya.
        </p>
    );
}
