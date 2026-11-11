export function ShopListSkeleton({ count }: { count: number }) {
    return (
        <div
            className="grid gap-6 pt-3 sm:grid-cols-2"
            aria-busy="true"
            aria-label="Memuat tempat printing"
        >
            {Array.from({ length: count }, (_, index) => (
                <div
                    key={index}
                    className="h-48 animate-pulse rounded-sm bg-muted"
                />
            ))}
        </div>
    );
}
