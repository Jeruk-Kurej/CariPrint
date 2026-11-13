import { useEffect, useRef, useState } from 'react';

/**
 * Memberi tahu apakah isi sebuah kotak bisa digeser ke samping dan apakah masih ada yang tersembunyi di kanan,
 * supaya pengguna diberi petunjuk bahwa tabelnya bisa digeser. Ikut berubah saat ukuran kotak atau isinya berubah.
 */
export function useScrollHint<T extends HTMLElement>() {
    const ref = useRef<T>(null);
    const [scrollable, setScrollable] = useState(false);
    const [atEnd, setAtEnd] = useState(true);

    useEffect(() => {
        const element = ref.current;

        if (element === null) {
            return;
        }

        function measure() {
            if (element === null) {
                return;
            }

            setScrollable(element.scrollWidth > element.clientWidth + 1);
            setAtEnd(
                element.scrollLeft + element.clientWidth >=
                    element.scrollWidth - 1,
            );
        }

        const observer = new ResizeObserver(measure);

        observer.observe(element);

        if (element.firstElementChild) {
            observer.observe(element.firstElementChild);
        }

        element.addEventListener('scroll', measure, { passive: true });
        measure();

        return () => {
            observer.disconnect();
            element.removeEventListener('scroll', measure);
        };
    }, []);

    return { ref, scrollable, hasMoreOnRight: scrollable && !atEnd };
}
