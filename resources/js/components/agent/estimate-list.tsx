import { useState } from 'react';
import { EstimateRow } from '@/components/agent/estimate-row';
import type { Estimate } from '@/components/agent/estimate-row';
import { EmptyState } from '@/components/directory/empty-state';
import { SegmentedControl } from '@/components/segmented-control';
import { SwitchField } from '@/components/switch-field';
import { ascendingWithMissingLast } from '@/lib/shop-filter';

export type { Estimate };

type EstimateSort = 'price' | 'distance';

const comparators: Record<EstimateSort, (a: Estimate, b: Estimate) => number> =
    {
        price: ascendingWithMissingLast((shop) => shop.total),
        distance: ascendingWithMissingLast((shop) => shop.distanceKm),
    };

/** Termurah di atas dan harganya distabilo. Bisa disaring ke yang masih buka dan diurutkan dari yang terdekat. */
export function EstimateList({ estimates }: { estimates: Estimate[] }) {
    const [openOnly, setOpenOnly] = useState(false);
    const [sort, setSort] = useState<EstimateSort>('price');
    const hasDistance = estimates.some((shop) => shop.distanceKm !== null);

    const shown = estimates
        .filter((shop) => !openOnly || shop.openState !== 'closed')
        .sort(comparators[hasDistance ? sort : 'price']);

    return (
        <div className="flex flex-col gap-3">
            <div className="flex flex-wrap items-center gap-4">
                <SwitchField
                    label="Masih buka saja"
                    checked={openOnly}
                    onChange={setOpenOnly}
                />
                <SegmentedControl
                    label="Urutkan hasil"
                    value={hasDistance ? sort : 'price'}
                    options={[
                        { value: 'price', label: 'Termurah' },
                        {
                            value: 'distance',
                            label: 'Terdekat',
                            disabled: !hasDistance,
                        },
                    ]}
                    onChange={setSort}
                />
            </div>
            {shown.length === 0 ? (
                <EmptyState
                    title="Tidak ada yang buka sekarang"
                    hint={'Matikan "Masih buka saja" untuk melihat semuanya.'}
                />
            ) : (
                <ul className="divide-y overflow-hidden paper-card">
                    {shown.map((shop) => (
                        <EstimateRow key={shop.slug} shop={shop} />
                    ))}
                </ul>
            )}
        </div>
    );
}
