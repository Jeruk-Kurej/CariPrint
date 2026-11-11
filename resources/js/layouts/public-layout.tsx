import type { ReactNode } from 'react';
import { PublicFooter } from '@/components/public-footer';
import { PublicHeader } from '@/components/public-header';

export default function PublicLayout({ children }: { children: ReactNode }) {
    return (
        <div className="flex min-h-screen flex-col overflow-x-clip bg-notebook text-foreground">
            <PublicHeader />
            <div className="mx-auto flex w-full max-w-[90rem] flex-1 flex-col lg:px-6">
                {children}
            </div>
            <PublicFooter />
        </div>
    );
}
