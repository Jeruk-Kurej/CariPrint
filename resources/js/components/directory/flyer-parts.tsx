const papers = [
    'bg-note-yellow',
    'bg-note-pink',
    'bg-note-mint',
    'bg-note-sky',
];

const tapes = ['tape-blue', 'tape-yellow', 'tape-pink'];

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
                className="absolute -top-2 left-1/2 size-4 -translate-x-1/2 pin"
            />
        );
    }

    return (
        <span
            aria-hidden
            className={`absolute -top-3 left-1/2 h-6 w-20 -translate-x-1/2 shadow-sm ${id % 2 === 0 ? '-rotate-3' : 'rotate-2'} ${tapes[id % tapes.length]}`}
        />
    );
}
