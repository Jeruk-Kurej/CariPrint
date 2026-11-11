export function EmptyState({ title, hint }: { title: string; hint?: string }) {
    return (
        <div className="flex flex-col items-center gap-1 rounded-xl border border-dashed p-8 text-center">
            <p className="font-medium">{title}</p>
            {hint && <p className="text-sm text-muted-foreground">{hint}</p>}
        </div>
    );
}
