import { Link } from '@inertiajs/react';

export function PublicHeader() {
    return (
        <header className="border-b bg-background">
            <div className="mx-auto flex max-w-3xl items-center justify-between px-4 py-3">
                <Link href="/" className="text-lg font-bold text-brand">
                    CariPrint
                </Link>
                <span className="text-xs text-muted-foreground">
                    Tempat printing di CitraLand
                </span>
            </div>
        </header>
    );
}
