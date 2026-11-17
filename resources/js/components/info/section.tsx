import type { ReactNode } from 'react';

/** Satu bagian di halaman informasi: judul dan isinya, selebar bacaan yang nyaman. */
export function Section({
    title,
    children,
}: {
    title: string;
    children: ReactNode;
}) {
    return (
        <section className="flex flex-col gap-3">
            <h2 className="font-display text-2xl font-bold tracking-tight">
                {title}
            </h2>
            <div className="flex max-w-2xl flex-col gap-3 text-muted-foreground">
                {children}
            </div>
        </section>
    );
}
