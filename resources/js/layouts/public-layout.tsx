import type { ReactNode } from 'react';
import { PublicFooter } from '@/components/public-footer';
import { PublicHeader } from '@/components/public-header';

export default function PublicLayout({ children }: { children: ReactNode }) {
    return (
        <div className="flex min-h-screen flex-col bg-background text-foreground">
            <PublicHeader />
            <div className="flex flex-1 flex-col">{children}</div>
            <PublicFooter />
        </div>
    );
}
