/** Placeholder cards while the list is being re-sorted by distance. */
export function ShopListSkeleton({ count = 3 }: { count?: number }) {
    return (
        <div
            className="grid gap-3"
            aria-busy="true"
            aria-label="Memuat daftar tempat printing"
        >
            {Array.from({ length: count }, (_, index) => (
                <div
                    key={index}
                    className="h-36 animate-pulse rounded-xl border bg-muted"
                />
            ))}
        </div>
    );
}
