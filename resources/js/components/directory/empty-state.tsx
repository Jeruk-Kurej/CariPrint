import type { ReactNode } from 'react';

/** Keadaan kosong. `children` untuk tindakan lanjutan, misalnya tautan ke formulir usul tempat. */
export function EmptyState({
    title,
    hint,
    children,
}: {
    title: string;
    hint?: string;
    children?: ReactNode;
}) {
    return (
        <div className="flex flex-col items-center gap-1 rounded-xl border border-dashed p-8 text-center">
            <p className="font-medium">{title}</p>
            {hint && <p className="text-sm text-muted-foreground">{hint}</p>}
            {children}
        </div>
    );
}
