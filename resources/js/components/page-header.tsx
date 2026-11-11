import type { ReactNode } from 'react';

/** Judul halaman: satu ukuran untuk semua halaman publik. */
export function PageTitle({ children }: { children: ReactNode }) {
    return (
        <h1 className="font-display text-4xl leading-[1.05] font-extrabold tracking-tight sm:text-5xl">
            {children}
        </h1>
    );
}

/** Judul bagian di dalam halaman. */
export function SectionHeading({
    id,
    children,
}: {
    id?: string;
    children: ReactNode;
}) {
    return (
        <h2
            id={id}
            className="font-display text-2xl font-extrabold tracking-tight sm:text-3xl"
        >
            {children}
        </h2>
    );
}

/** Kepala halaman: judul, kalimat pengantar, dan isi tambahan kalau perlu. */
export function PageHeader({
    title,
    intro,
    children,
}: {
    title: string;
    intro?: string;
    children?: ReactNode;
}) {
    return (
        <header className="flex flex-col gap-3">
            <PageTitle>{title}</PageTitle>
            {intro && (
                <p className="max-w-2xl text-lg text-muted-foreground">
                    {intro}
                </p>
            )}
            {children}
        </header>
    );
}
