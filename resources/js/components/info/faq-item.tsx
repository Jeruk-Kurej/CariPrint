import { ChevronDown } from 'lucide-react';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';

export function FaqItem({
    question,
    children,
}: {
    question: string;
    children: React.ReactNode;
}) {
    return (
        <Collapsible className="group border-b">
            <CollapsibleTrigger className="flex w-full items-center justify-between gap-4 py-4 text-left font-display text-lg font-semibold">
                {question}
                <ChevronDown
                    className="size-5 shrink-0 text-muted-foreground transition-transform group-data-[state=open]:rotate-180"
                    aria-hidden
                />
            </CollapsibleTrigger>
            <CollapsibleContent className="pb-4 text-muted-foreground">
                {children}
            </CollapsibleContent>
        </Collapsible>
    );
}
