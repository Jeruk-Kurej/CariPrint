import { MessageCircle } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { whatsappUrl } from '@/lib/whatsapp';

type Props = {
    phone: string;
    message?: string;
};

export function WhatsAppButton({ phone, message }: Props) {
    return (
        <Button asChild size="sm" variant="outline" className="rounded-full">
            <a
                href={whatsappUrl(phone, message)}
                target="_blank"
                rel="noopener noreferrer"
            >
                <MessageCircle />
                WhatsApp
            </a>
        </Button>
    );
}
