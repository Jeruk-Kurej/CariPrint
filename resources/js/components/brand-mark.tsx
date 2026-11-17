/**
 * Logo CariPrint: pamflet di papan pengumuman dengan selotip kuning dan kaca pembesar bertinta stabilo.
 * Gambarnya sama dengan public/favicon.svg, tetapi warnanya memakai token tema.
 */
export function BrandMark({ className = 'size-7' }: { className?: string }) {
    return (
        <svg
            viewBox="0 0 512 512"
            className={className}
            fill="none"
            aria-hidden
        >
            <rect width="512" height="512" rx="112" fill="var(--brand)" />
            <g transform="rotate(-8 256 262)">
                <path
                    d="M146 92h196a28 28 0 0 1 28 28v236L290 436H146a28 28 0 0 1-28-28V120a28 28 0 0 1 28-28z"
                    fill="var(--card)"
                />
                <path
                    d="M370 356l-80 80v-52a28 28 0 0 1 28-28z"
                    fill="var(--accent)"
                />
                <rect
                    x="196"
                    y="62"
                    width="120"
                    height="48"
                    rx="8"
                    fill="var(--highlight)"
                    transform="rotate(4 256 86)"
                />
                <rect
                    x="154"
                    y="372"
                    width="108"
                    height="20"
                    rx="10"
                    fill="var(--accent)"
                />
                <rect
                    x="154"
                    y="404"
                    width="68"
                    height="20"
                    rx="10"
                    fill="var(--accent)"
                />
                <circle
                    cx="232"
                    cy="232"
                    r="60"
                    fill="var(--highlight)"
                    stroke="var(--foreground)"
                    strokeWidth="26"
                />
                <path
                    d="M278 278l52 52"
                    stroke="var(--foreground)"
                    strokeWidth="32"
                    strokeLinecap="round"
                />
            </g>
        </svg>
    );
}
