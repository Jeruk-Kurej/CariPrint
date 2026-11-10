const dayLabels: Record<string, string> = {
    mon: 'Senin',
    tue: 'Selasa',
    wed: 'Rabu',
    thu: 'Kamis',
    fri: 'Jumat',
    sat: 'Sabtu',
    sun: 'Minggu',
};

type Slot = { open: string; close: string };

/** Opening hours per day. Today is highlighted so students can see at a glance. */
export function OpeningHoursList({
    hours,
    today,
}: {
    hours: Record<string, Slot[]>;
    today: string;
}) {
    return (
        <ul className="divide-y rounded-xl border text-sm">
            {Object.entries(dayLabels).map(([key, label]) => {
                const slots = hours[key] ?? [];

                return (
                    <li
                        key={key}
                        className={`flex justify-between px-3 py-2 ${key === today ? 'font-semibold text-brand' : ''}`}
                    >
                        <span>{label}</span>
                        <span>
                            {slots.length === 0
                                ? 'Tutup'
                                : slots
                                      .map(
                                          (slot) =>
                                              `${slot.open}–${slot.close}`,
                                      )
                                      .join(', ')}
                        </span>
                    </li>
                );
            })}
        </ul>
    );
}
