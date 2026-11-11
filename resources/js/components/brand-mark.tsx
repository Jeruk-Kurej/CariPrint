/** Tanda registrasi cetak: lingkaran dengan garis silang, dipakai sebagai logo. */
export function BrandMark({ className = 'size-6' }: { className?: string }) {
    return (
        <svg
            viewBox="0 0 24 24"
            className={`text-brand ${className}`}
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
