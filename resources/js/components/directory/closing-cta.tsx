import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { index as directoryIndex } from '@/routes/directory';

export function ClosingCta() {
    return (
        <section className="flex flex-col items-start gap-4 rounded-lg bg-primary p-6 text-primary-foreground sm:p-10">
            <h2 className="max-w-xl font-display text-3xl leading-tight font-extrabold tracking-tight sm:text-4xl">
                Sebelum berangkat ke tempat print, cek dulu yang buka.
            </h2>
            <Button
                asChild
                size="lg"
                className="bg-highlight text-highlight-foreground hover:bg-highlight/90"
            >
                <Link href={directoryIndex().url}>Lihat semua tempat</Link>
            </Button>
        </section>
    );
}
