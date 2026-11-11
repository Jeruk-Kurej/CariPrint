import { Link } from '@inertiajs/react';
import { Menu } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { aboutNav, homeUrl, mainNav } from '@/lib/navigation';

/** Tanda registrasi cetak: lingkaran dengan garis silang, dipakai sebagai logo. */
function RegistrationMark() {
    return (
        <svg
            viewBox="0 0 24 24"
            className="size-6 text-brand"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.75"
            aria-hidden
        >
            <circle cx="12" cy="12" r="7.5" />
            <path d="M12 1.5v5M12 17.5v5M1.5 12h5M17.5 12h5" />
            <circle cx="12" cy="12" r="2.25" fill="currentColor" />
        </svg>
    );
}

export function PublicHeader() {
    const [open, setOpen] = useState(false);
    const mobileLinks = [...mainNav, ...aboutNav.slice(1)];

    return (
        <header className="sticky top-0 z-20 border-b bg-background/95 backdrop-blur">
            <div className="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
                <Link href={homeUrl} className="flex items-center gap-2">
                    <RegistrationMark />
                    <span className="font-display text-lg font-bold tracking-tight">
                        CariPrint
                    </span>
                </Link>

                <nav
                    aria-label="Menu utama"
                    className="hidden items-center gap-6 text-sm font-medium md:flex"
                >
                    {mainNav.map((link) => (
                        <Link
                            key={link.href}
                            href={link.href}
                            className="text-muted-foreground hover:text-foreground"
                        >
                            {link.label}
                        </Link>
                    ))}
                </nav>

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
                            {mobileLinks.map((link) => (
                                <Link
                                    key={link.href}
                                    href={link.href}
                                    onClick={() => setOpen(false)}
                                    className="rounded-md px-3 py-3 text-base font-medium hover:bg-accent"
                                >
                                    {link.label}
                                </Link>
                            ))}
                        </nav>
                    </SheetContent>
                </Sheet>
            </div>
        </header>
    );
}
