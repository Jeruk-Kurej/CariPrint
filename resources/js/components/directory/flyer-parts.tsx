const papers = [
    'bg-note-yellow',
    'bg-note-pink',
    'bg-note-mint',
    'bg-note-sky',
];

const tapes = [
    'bg-[repeating-linear-gradient(135deg,rgba(53,88,240,0.3)_0_6px,rgba(53,88,240,0.16)_6px_12px)]',
    'bg-[repeating-linear-gradient(135deg,rgba(255,214,77,0.75)_0_6px,rgba(255,214,77,0.45)_6px_12px)]',
    'bg-[repeating-linear-gradient(135deg,rgba(255,120,160,0.4)_0_6px,rgba(255,120,160,0.22)_6px_12px)]',
];

/** Bayangan kertas yang menempel di papan: tipis di tepi, lembut di bawah. */
export const paperShadow =
    'shadow-[0_1px_0_rgba(29,33,71,0.15),0_10px_20px_-10px_rgba(29,33,71,0.35)]';

/** Warna kertas tetap untuk satu tempat, supaya tidak berganti tiap dibuka. */
export function paperFor(id: number): string {
    return papers[id % papers.length];
}

/** Paku payung atau selotip kertas, bergantian menurut id tempat. */
export function Fastener({ id }: { id: number }) {
    if (id % 3 === 0) {
        return (
            <span
                aria-hidden
                className="absolute -top-2 left-1/2 size-4 -translate-x-1/2 rounded-full bg-primary shadow-[0_2px_0_rgba(0,0,0,0.25)] ring-2 ring-white"
            />
        );
    }

    return (
        <span
            aria-hidden
            className={`absolute -top-3 left-1/2 h-6 w-20 -translate-x-1/2 ${id % 2 === 0 ? '-rotate-3' : 'rotate-2'} shadow-[0_1px_2px_rgba(29,33,71,0.2)] ${tapes[id % tapes.length]}`}
        />
    );
}
