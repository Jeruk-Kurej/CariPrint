import { Link, router } from '@inertiajs/react';
import {
    ChevronDown,
    LogOut,
    PlusCircle,
    ShieldCheck,
    UserRound,
} from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useInitials } from '@/hooks/use-initials';
import { logout } from '@/routes';
import { account } from '@/routes';
import { create as suggest } from '@/routes/suggestions';
import type { User } from '@/types';

/** Stiker inisial: pengganti foto, karena CariPrint tidak menyimpan foto profil Google. */
export function AvatarSticker({
    name,
    className = 'size-9 text-sm',
}: {
    name: string;
    className?: string;
}) {
    const initials = useInitials();

    return (
        <span
            aria-hidden
            className={`inline-flex shrink-0 items-center justify-center rounded-full border-2 border-foreground bg-brand font-display font-extrabold text-brand-foreground ${className}`}
        >
            {initials(name)}
        </span>
    );
}

export function isAdmin(user: User): boolean {
    return user.role === 'admin';
}

function firstName(user: User): string {
    return user.name.trim().split(/\s+/u)[0] ?? user.name;
}

/** Menu akun di header: avatar dan nama, lalu daftar pilihan saat diklik. */
export function AccountMenu({ user }: { user: User }) {
    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <button
                    type="button"
                    aria-label={`Menu akun ${user.name}`}
                    className="flex items-center gap-2 rounded-full border-2 border-foreground/15 bg-card py-1 pr-3 pl-1 text-sm font-semibold outline-none hover:border-primary focus-visible:ring-2 focus-visible:ring-ring data-[state=open]:border-primary"
                >
                    <AvatarSticker
                        name={user.name}
                        className="size-8 text-xs"
                    />
                    <span className="max-w-28 truncate">{firstName(user)}</span>
                    <ChevronDown
                        aria-hidden
                        className="size-4 text-muted-foreground"
                    />
                </button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-64 paper-card p-1.5">
                <DropdownMenuLabel className="flex items-center gap-3 p-2">
                    <AvatarSticker name={user.name} />
                    <span className="min-w-0">
                        <span className="block truncate text-sm font-bold">
                            {user.name}
                        </span>
                        <span className="block truncate text-xs font-normal text-muted-foreground">
                            {user.email}
                        </span>
                    </span>
                </DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem
                    asChild
                    className="cursor-pointer gap-2.5 rounded-md py-2.5"
                >
                    <Link href={account().url}>
                        <UserRound aria-hidden /> Akunku
                    </Link>
                </DropdownMenuItem>
                <DropdownMenuItem
                    asChild
                    className="cursor-pointer gap-2.5 rounded-md py-2.5"
                >
                    <Link href={suggest().url}>
                        <PlusCircle aria-hidden /> Usulkan tempat
                    </Link>
                </DropdownMenuItem>
                {isAdmin(user) && (
                    <DropdownMenuItem
                        asChild
                        className="cursor-pointer gap-2.5 rounded-md py-2.5"
                    >
                        <a href="/admin">
                            <ShieldCheck aria-hidden /> Panel admin
                        </a>
                    </DropdownMenuItem>
                )}
                <DropdownMenuSeparator />
                <DropdownMenuItem
                    asChild
                    className="cursor-pointer gap-2.5 rounded-md py-2.5"
                >
                    <Link
                        href={logout()}
                        as="button"
                        onClick={() => router.flushAll()}
                        className="w-full"
                    >
                        <LogOut aria-hidden /> Keluar
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}

/** Versi daftar untuk menu di HP: kartu akun, lalu pilihan yang sama. */
export function AccountList({
    user,
    onNavigate,
}: {
    user: User;
    onNavigate: () => void;
}) {
    const item =
        'flex w-full items-center gap-3 rounded-md px-3 py-3 text-left text-base font-medium hover:bg-accent';

    return (
        <div className="mt-2 flex flex-col gap-1 border-t px-2 pt-4">
            <div className="flex items-center gap-3 px-3 pb-2">
                <AvatarSticker name={user.name} className="size-11 text-base" />
                <span className="min-w-0">
                    <span className="block truncate font-bold">
                        {user.name}
                    </span>
                    <span className="block truncate text-sm text-muted-foreground">
                        {user.email}
                    </span>
                </span>
            </div>
            <Link href={account().url} onClick={onNavigate} className={item}>
                <UserRound aria-hidden className="size-5" /> Akunku
            </Link>
            <Link href={suggest().url} onClick={onNavigate} className={item}>
                <PlusCircle aria-hidden className="size-5" /> Usulkan tempat
            </Link>
            {isAdmin(user) && (
                <a href="/admin" className={item}>
                    <ShieldCheck aria-hidden className="size-5" /> Panel admin
                </a>
            )}
            <Link
                href={logout()}
                as="button"
                onClick={() => router.flushAll()}
                className={item}
            >
                <LogOut aria-hidden className="size-5" /> Keluar
            </Link>
        </div>
    );
}
