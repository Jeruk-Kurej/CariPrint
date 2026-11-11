import { router } from '@inertiajs/react';
import type { FormEvent, KeyboardEvent } from 'react';
import { useRef, useState } from 'react';
import { parse } from '@/routes/agent';

export const MAX_REQUEST_LENGTH = 500;

const EMPTY_MESSAGE =
    'Tulis dulu kebutuhan cetakmu, misalnya jumlah halaman, hitam putih atau warna, dan jilidnya.';

/**
 * Logika form asisten cetak. Tombolnya selalu bisa ditekan: kalau kotak masih kosong,
 * pengguna diberi tahu apa yang perlu ditulis, bukan dihadapkan pada tombol yang mati.
 */
export function usePrintRequest() {
    const [request, setRequest] = useState('');
    const [processing, setProcessing] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const field = useRef<HTMLTextAreaElement>(null);

    function change(value: string) {
        setRequest(value);
        setError(null);
    }

    function pick(example: string) {
        change(example);
        field.current?.focus();
    }

    function submit(event?: FormEvent) {
        event?.preventDefault();

        if (processing) {
            return;
        }

        if (request.trim() === '') {
            setError(EMPTY_MESSAGE);
            field.current?.focus();

            return;
        }

        setError(null);
        router.post(
            parse().url,
            { request },
            {
                preserveScroll: true,
                preserveState: true,
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onError: (errors) => setError(errors.request ?? null),
            },
        );
    }

    function submitOnShortcut(event: KeyboardEvent) {
        if (event.key === 'Enter' && (event.metaKey || event.ctrlKey)) {
            submit();
        }
    }

    return {
        request,
        change,
        pick,
        submit,
        submitOnShortcut,
        processing,
        error,
        field,
    };
}
