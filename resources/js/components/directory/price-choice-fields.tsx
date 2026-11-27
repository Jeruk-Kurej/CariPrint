import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { colorChoiceLabels, paperLabels } from '@/lib/price-choice';
import type { PriceChoice } from '@/lib/price-choice';

type FieldProps<T extends string> = {
    label: string;
    value: T;
    options: Record<T, string>;
    onChange: (value: T) => void;
};

function ChoiceSelect<T extends string>({
    label,
    value,
    options,
    onChange,
}: FieldProps<T>) {
    return (
        <Select value={value} onValueChange={(next) => onChange(next as T)}>
            <SelectTrigger
                aria-label={label}
                className="w-full rounded-full border-2 border-foreground/15 bg-card px-3 font-semibold data-[size=default]:h-11"
            >
                <SelectValue />
            </SelectTrigger>
            <SelectContent>
                {(Object.keys(options) as T[]).map((option) => (
                    <SelectItem
                        key={option}
                        value={option}
                        className="min-h-11"
                    >
                        {options[option]}
                    </SelectItem>
                ))}
            </SelectContent>
        </Select>
    );
}

/** Ukuran kertas dan warna. Berdampingan supaya hanya memakan satu baris di ponsel. */
export function PriceChoiceFields({
    value,
    onChange,
}: {
    value: PriceChoice;
    onChange: (changes: Partial<PriceChoice>) => void;
}) {
    return (
        <div className="grid grid-cols-2 gap-2 sm:max-w-md">
            <ChoiceSelect
                label="Ukuran kertas"
                value={value.paper}
                options={paperLabels}
                onChange={(paper) => onChange({ paper })}
            />
            <ChoiceSelect
                label="Hitam-putih atau warna"
                value={value.color}
                options={colorChoiceLabels}
                onChange={(color) => onChange({ color })}
            />
        </div>
    );
}
