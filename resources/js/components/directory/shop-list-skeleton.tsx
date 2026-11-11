export function ShopListSkeleton({ count }: { count: number }) {
    return (
        <div
            className="grid gap-3"
            aria-busy="true"
            aria-label="Memuat tempat printing"
        >
            {Array.from({ length: count }, (_, index) => (
                <div
                    key={index}
                    className="h-28 animate-pulse rounded-lg border bg-muted"
                />
            ))}
        </div>
    );
}
