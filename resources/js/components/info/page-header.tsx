export function PageHeader({ title, intro }: { title: string; intro: string }) {
    return (
        <header className="flex flex-col gap-3 border-b pb-8">
            <h1 className="font-display text-4xl leading-[1.05] font-extrabold tracking-tight sm:text-5xl">
                {title}
            </h1>
            <p className="max-w-2xl text-lg text-muted-foreground">{intro}</p>
        </header>
    );
}

export function Section({
    title,
    children,
}: {
    title: string;
    children: React.ReactNode;
}) {
    return (
        <section className="flex flex-col gap-3">
            <h2 className="font-display text-2xl font-bold tracking-tight">
                {title}
            </h2>
            <div className="flex max-w-2xl flex-col gap-3 text-muted-foreground">
                {children}
            </div>
        </section>
    );
}
