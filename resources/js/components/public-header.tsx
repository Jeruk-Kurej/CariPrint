import { Link, router, usePage } from '@inertiajs/react';
import { Menu } from 'lucide-react';
import { useState } from 'react';
import { BrandMark } from '@/components/brand-mark';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { homeUrl, mainNav } from '@/lib/navigation';
import { login, logout } from '@/routes';
import type { User } from '@/types';

export function PublicHeader() {
    const [open, setOpen] = useState(false);
    const { url, props } = usePage();
    const path = url.split('?')[0];
    const user = props.auth.user as User | null;

    return (
        <header className="sticky top-0 z-20 border-b bg-background/95 backdrop-blur">
            <div className="mx-auto flex max-w-[90rem] items-center justify-between gap-4 px-4 py-3 lg:px-10">
                <Link href={homeUrl} className="flex items-center gap-2">
                    <BrandMark />
                    <span className="font-display text-lg font-bold tracking-tight">
                        CariPrint
                    </span>
                </Link>

                <nav
                    aria-label="Menu utama"
                    className="hidden items-center gap-1 text-sm font-semibold md:flex"
                >
                    {mainNav.map((link) => (
                        <Link
                            key={link.href}
                            href={link.href}
                            aria-current={
                                path === link.href ? 'page' : undefined
                            }
                            className={`rounded-full px-3.5 py-1.5 ${path === link.href ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-foreground'}`}
                        >
                            {link.label}
                        </Link>
                    ))}
                </nav>

                <div className="hidden items-center gap-2 md:flex">
                    {user ? (
                        <Link
                            href={logout()}
                            as="button"
                            onClick={() => router.flushAll()}
                            className="rounded-full px-3.5 py-1.5 text-sm font-semibold text-muted-foreground hover:bg-accent hover:text-foreground"
                        >
                            Keluar
                        </Link>
                    ) : (
                        <Link
                            href={login()}
                            className="rounded-full px-3.5 py-1.5 text-sm font-semibold text-muted-foreground hover:bg-accent hover:text-foreground"
                        >
                            Masuk
                        </Link>
                    )}
                </div>

                <Sheet open={open} onOpenChange={setOpen}>
                    <SheetTrigger asChild>
                        <Button
                            variant="ghost"
                            size="icon"
                            className="md:hidden"
                            aria-label="Buka menu"
                        >
                            <Menu />
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="right" className="w-72">
                        <SheetTitle className="px-4 pt-4 font-display">
                            Menu
                        </SheetTitle>
                        <SheetDescription className="sr-only">
                            Daftar halaman CariPrint
                        </SheetDescription>
                        <nav
                            aria-label="Menu seluler"
                            className="flex flex-col px-2"
                        >
                            {mainNav.map((link) => (
                                <Link
                                    key={link.href}
                                    href={link.href}
                                    onClick={() => setOpen(false)}
                                    className="rounded-md px-3 py-3 text-base font-medium hover:bg-accent"
                                >
                                    {link.label}
                                </Link>
                            ))}
                            {user ? (
                                <Link
                                    href={logout()}
                                    as="button"
                                    onClick={() => router.flushAll()}
                                    className="rounded-md px-3 py-3 text-left text-base font-medium hover:bg-accent"
                                >
                                    Keluar
                                </Link>
                            ) : (
                                <Link
                                    href={login()}
                                    onClick={() => setOpen(false)}
                                    className="rounded-md px-3 py-3 text-base font-medium hover:bg-accent"
                                >
                                    Masuk
                                </Link>
                            )}
                        </nav>
                    </SheetContent>
                </Sheet>
            </div>
        </header>
    );
}
