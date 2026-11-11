import { Link } from '@inertiajs/react';
import { BrandMark } from '@/components/brand-mark';
import { home } from '@/routes';
import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <div className="flex min-h-svh flex-col items-center justify-center gap-6 bg-notebook p-6 md:p-10">
            <div className="w-full max-w-sm">
                <div className="flex flex-col gap-8 rounded-sm bg-card p-6 paper-shadow sm:p-8">
                    <div className="flex flex-col items-center gap-4">
                        <Link
                            href={home()}
                            className="flex items-center gap-2 font-display text-xl font-extrabold tracking-tight"
                        >
                            <BrandMark className="size-8" />
                            CariPrint
                        </Link>

                        <div className="space-y-2 text-center">
                            <h1 className="font-display text-2xl font-extrabold tracking-tight">
                                {title}
                            </h1>
                            <p className="text-center text-sm text-muted-foreground">
                                {description}
                            </p>
                        </div>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}
