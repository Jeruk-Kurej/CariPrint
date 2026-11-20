import { Link } from '@inertiajs/react';
import { login } from '@/routes';

export type AiQuotaStatus = {
    limit: number | null;
    remaining: number | null;
    isGuest: boolean;
};

/** Sisa jatah asisten hari ini. Admin tidak dibatasi, jadi tidak ada yang ditampilkan. */
export function QuotaNote({ quota }: { quota: AiQuotaStatus }) {
    if (quota.limit === null || quota.remaining === null) {
        return null;
    }

    return (
        <p aria-live="polite" className="text-sm text-foreground/70">
            Sisa {quota.remaining} dari {quota.limit} permintaan hari ini.
            {quota.isGuest && (
                <>
                    {' '}
                    <Link
                        href={login()}
                        className="font-medium text-foreground underline"
                    >
                        Masuk
                    </Link>{' '}
                    untuk jatah lebih banyak.
                </>
            )}
        </p>
    );
}
