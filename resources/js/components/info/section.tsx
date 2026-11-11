import type { ReactNode } from 'react';
import { SectionHeading } from '@/components/page-header';

/** Satu bagian halaman informasi: judul dan paragraf dengan lebar baris yang nyaman dibaca. */
export function Section({
    title,
    children,
}: {
    title: string;
    children: ReactNode;
}) {
    return (
        <section className="flex flex-col gap-3">
            <SectionHeading>{title}</SectionHeading>
            <div className="flex max-w-2xl flex-col gap-3 text-muted-foreground">
                {children}
            </div>
        </section>
    );
}
