type Option<T extends string> = {
    value: T;
    label: string;
    disabled?: boolean;
};

type Props<T extends string> = {
    label: string;
    value: T;
    options: Option<T>[];
    onChange: (value: T) => void;
};

/** Pilih salah satu dari beberapa opsi yang saling meniadakan, misalnya urutan daftar. */
export function SegmentedControl<T extends string>({
    label,
    value,
    options,
    onChange,
}: Props<T>) {
    return (
        <div
            role="radiogroup"
            aria-label={label}
            className="inline-flex rounded-full border-2 border-foreground/15 bg-card p-1"
        >
            {options.map((option) => (
                <button
                    key={option.value}
                    type="button"
                    role="radio"
                    aria-checked={value === option.value}
                    disabled={option.disabled}
                    onClick={() => onChange(option.value)}
                    className={`rounded-full px-4 py-1.5 text-sm font-semibold transition-colors focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none disabled:pointer-events-none disabled:opacity-40 ${value === option.value ? 'bg-primary text-primary-foreground' : 'hover:bg-accent'}`}
                >
                    {option.label}
                </button>
            ))}
        </div>
    );
}
