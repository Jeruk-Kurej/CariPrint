import { useCallback, useState } from 'react';

/**
 * Keeps the list and the map in sync: picking a shop on either one selects it on both.
 */
export function useSelectedShop() {
    const [selectedId, setSelectedId] = useState<number | null>(null);
    const select = useCallback((id: number | null) => setSelectedId(id), []);

    return { selectedId, select };
}
