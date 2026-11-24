import { Head, Link, router, usePage } from '@inertiajs/react';
import { AvatarSticker } from '@/components/account-menu';
import { DeleteAccount } from '@/components/account/delete-account';
import { SuggestionStatusBadge } from '@/components/account/suggestion-status-badge';
import { EmptyState } from '@/components/directory/empty-state';
import { Fastener } from '@/components/directory/flyer-parts';
import { SectionHeading } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { logout } from '@/routes';
import { create as suggest } from '@/routes/suggestions';
import type { User } from '@/types';

type Suggestion = {
    id: number;
    name: string;
    address: string;
    status: string;
    statusLabel: string;
    adminNote: string | null;
    sentAt: string;
};

type Props = { suggestions: Suggestion[]; isAdmin: boolean };

/** Halaman akun: sapaan di kertas catatan, lalu usulan tempat yang pernah dikirim beserta statusnya. */
export default function AccountShow({ suggestions, isAdmin }: Props) {
    const user = usePage().props.auth.user as User;
    const firstName = user.name.trim().split(/\s+/u)[0] ?? user.name;

    return (
        <>
            <Head title="Akunku" />
            <main className="flex flex-col gap-10 px-4 py-8 sm:py-12">
                <section
                    aria-label="Akunmu"
                    className="relative flex max-w-3xl flex-col gap-5 rounded-sm bg-note-yellow p-6 paper-shadow sm:p-8"
                >
                    <Fastener id={3} />
                    <div className="flex items-center gap-4">
                        <AvatarSticker
                            name={user.name}
                            className="size-16 text-2xl"
                        />
                        <div className="min-w-0">
                            <h1 className="font-display text-3xl leading-tight font-extrabold tracking-tight sm:text-4xl">
                                Halo, {firstName}
                            </h1>
                            <p className="truncate text-foreground/70">
                                {user.email}
                            </p>
                        </div>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <Button asChild>
                            <Link href={suggest().url}>Usulkan tempat</Link>
                        </Button>
                        {isAdmin && (
                            <Button asChild variant="outline">
                                <a href="/admin">Buka panel admin</a>
                            </Button>
                        )}
                        <Button
                            variant="ghost"
                            onClick={() => {
                                router.flushAll();
                                router.post(logout().url);
                            }}
                        >
                            Keluar
                        </Button>
                    </div>
                </section>

                <section
                    aria-labelledby="usulanmu"
                    className="flex max-w-3xl flex-col gap-4"
                >
                    <SectionHeading id="usulanmu">
                        Usulan tempatmu
                    </SectionHeading>

                    {suggestions.length === 0 ? (
                        <EmptyState
                            title="Belum ada usulan"
                            hint="Tahu tempat print yang belum ada di CariPrint? Kirim datanya, dan kamu akan dikabari lewat email."
                        >
                            <Link
                                href={suggest().url}
                                className="mt-2 text-sm font-semibold text-primary hover:underline"
                            >
                                Usulkan tempat pertamamu
                            </Link>
                        </EmptyState>
                    ) : (
                        <ul className="flex flex-col gap-3">
                            {suggestions.map((suggestion) => (
                                <li
                                    key={suggestion.id}
                                    className="flex flex-col gap-3 paper-card p-4 sm:flex-row sm:items-start sm:justify-between"
                                >
                                    <div className="min-w-0">
                                        <p className="font-display text-lg leading-snug font-bold">
                                            {suggestion.name}
                                        </p>
                                        <p className="text-sm text-muted-foreground">
                                            {suggestion.address}
                                        </p>
                                        <p className="mt-1 text-xs text-muted-foreground">
                                            Dikirim {suggestion.sentAt}
                                        </p>
                                        {suggestion.adminNote && (
                                            <p className="mt-3 rounded-sm bg-secondary px-3 py-2 text-sm">
                                                <span className="font-semibold">
                                                    Catatan admin:
                                                </span>{' '}
                                                {suggestion.adminNote}
                                            </p>
                                        )}
                                    </div>
                                    <SuggestionStatusBadge
                                        status={suggestion.status}
                                        label={suggestion.statusLabel}
                                    />
                                </li>
                            ))}
                        </ul>
                    )}
                </section>

                {!isAdmin && <DeleteAccount email={user.email} />}
            </main>
        </>
    );
}
