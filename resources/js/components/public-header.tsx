import { Link, usePage } from '@inertiajs/react';
import { Menu } from 'lucide-react';
import { useState } from 'react';
import { AccountList, AccountMenu } from '@/components/account-menu';
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
import { login } from '@/routes';
import { create as suggest } from '@/routes/suggestions';
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
                        <AccountMenu user={user} />
                    ) : (
                        <>
                            <Link
                                href={suggest()}
                                className="px-2 text-sm font-semibold text-muted-foreground hover:text-foreground"
                            >
                                Usulkan tempat
                            </Link>
                            <Button asChild variant="outline" size="sm">
                                <Link href={login()}>Masuk</Link>
                            </Button>
                        </>
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
                                    aria-current={
                                        path === link.href ? 'page' : undefined
                                    }
                                    className={`rounded-md px-3 py-3 text-base font-medium ${path === link.href ? 'bg-primary text-primary-foreground' : 'hover:bg-accent'}`}
                                >
                                    {link.label}
                                </Link>
                            ))}
                        </nav>
                        {user ? (
                            <AccountList
                                user={user}
                                onNavigate={() => setOpen(false)}
                            />
                        ) : (
                            <div className="flex flex-col gap-2 px-4 pt-4">
                                <Button
                                    asChild
                                    variant="outline"
                                    className="w-full"
                                >
                                    <Link
                                        href={suggest()}
                                        onClick={() => setOpen(false)}
                                    >
                                        Usulkan tempat
                                    </Link>
                                </Button>
                                <Button asChild className="w-full">
                                    <Link
                                        href={login()}
                                        onClick={() => setOpen(false)}
                                    >
                                        Masuk
                                    </Link>
                                </Button>
                            </div>
                        )}
                    </SheetContent>
                </Sheet>
            </div>
        </header>
    );
}
