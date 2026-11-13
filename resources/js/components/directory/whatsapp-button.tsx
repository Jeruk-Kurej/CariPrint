import { MessageCircle } from 'lucide-react';
import { Button } from '@/components/ui/button';

type Props = {
    phone: string;
    message?: string;
};

export function WhatsAppButton({ phone, message }: Props) {
    const query = message ? `?text=${encodeURIComponent(message)}` : '';

    return (
        <Button asChild size="sm" variant="outline" className="rounded-full">
            <a
                href={`https://wa.me/${phone}${query}`}
                target="_blank"
                rel="noopener noreferrer"
            >
                <MessageCircle />
                WhatsApp
            </a>
        </Button>
    );
}
