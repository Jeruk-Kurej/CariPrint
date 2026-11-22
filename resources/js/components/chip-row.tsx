import type { ReactNode } from 'react';
import { useScrollHint } from '@/hooks/use-scroll-hint';

/**
 * Deretan chip satu baris yang bisa digeser di ponsel supaya tidak memakan banyak tinggi, dan membungkus di layar lebar.
 * Bayangan di kanan muncul selama masih ada chip yang tersembunyi.
 */
export function ChipRow({
    label,
    children,
}: {
    label: string;
    children: ReactNode;
}) {
    const scroll = useScrollHint<HTMLDivElement>();

    return (
        <div className="relative">
            <div
                ref={scroll.ref}
                role="group"
                aria-label={label}
                className="-mx-1 flex [scrollbar-width:none] items-center gap-2 overflow-x-auto px-1 py-0.5 lg:flex-wrap lg:overflow-visible [&::-webkit-scrollbar]:hidden"
            >
                {children}
            </div>
            {scroll.hasMoreOnRight && (
                <span
                    aria-hidden
                    className="pointer-events-none absolute inset-y-0 right-0 w-10 bg-gradient-to-l from-card to-transparent lg:hidden"
                />
            )}
        </div>
    );
}
