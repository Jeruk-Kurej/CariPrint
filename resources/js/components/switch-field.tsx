type Props = {
    label: string;
    checked: boolean;
    onChange: (checked: boolean) => void;
};

/** Sakelar untuk pilihan ya atau tidak, misalnya "hanya yang masih buka". */
export function SwitchField({ label, checked, onChange }: Props) {
    return (
        <label className="inline-flex cursor-pointer items-center gap-3 text-sm font-semibold">
            <button
                type="button"
                role="switch"
                aria-checked={checked}
                onClick={() => onChange(!checked)}
                className={`relative h-7 w-12 shrink-0 rounded-full border-2 transition-colors focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none ${checked ? 'border-primary bg-primary' : 'border-foreground/25 bg-card'}`}
            >
                <span
                    aria-hidden
                    className={`absolute top-0.5 left-0.5 size-5 rounded-full transition-transform ${checked ? 'translate-x-5 bg-primary-foreground' : 'bg-foreground/40'}`}
                />
            </button>
            {label}
        </label>
    );
}
