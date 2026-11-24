import { Form } from '@inertiajs/react';
import { useRef, useState } from 'react';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import InputError from '@/components/input-error';
import { SectionHeading } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

/**
 * Hapus akun sendiri. Konfirmasinya mengetik email akun, karena akun Google tidak punya kata sandi
 * yang bisa diketik. Usulan tempat dari email itu ikut terhapus.
 */
export function DeleteAccount({ email }: { email: string }) {
    const emailInput = useRef<HTMLInputElement>(null);
    const [typed, setTyped] = useState('');
    const matches = typed.trim().toLowerCase() === email.toLowerCase();

    return (
        <section
            aria-labelledby="hapus-akun"
            className="flex max-w-3xl flex-col gap-3"
        >
            <SectionHeading id="hapus-akun">Hapus akun</SectionHeading>
            <p className="text-muted-foreground">
                Akunmu dan semua usulan tempat yang kamu kirim dari email ini
                terhapus permanen dan tidak bisa dikembalikan.
            </p>

            <Dialog onOpenChange={() => setTyped('')}>
                <DialogTrigger asChild>
                    <Button
                        variant="outline"
                        className="min-h-11 self-start border-destructive text-destructive"
                        data-test="delete-account-button"
                    >
                        Hapus akunku
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <DialogTitle>Yakin mau menghapus akunmu?</DialogTitle>
                    <DialogDescription>
                        Akun dan semua usulan tempatmu ikut terhapus permanen.
                        Ketik emailmu ({email}) untuk melanjutkan.
                    </DialogDescription>

                    <Form
                        {...ProfileController.destroy.form()}
                        options={{ preserveScroll: true }}
                        onError={() => emailInput.current?.focus()}
                        className="space-y-6"
                    >
                        {({ processing, errors }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label htmlFor="confirm-email">Email</Label>
                                    <Input
                                        id="confirm-email"
                                        ref={emailInput}
                                        name="email"
                                        type="email"
                                        value={typed}
                                        onChange={(event) =>
                                            setTyped(event.target.value)
                                        }
                                        placeholder={email}
                                        autoComplete="off"
                                        className="min-h-11"
                                    />
                                    <InputError message={errors.email} />
                                </div>

                                <DialogFooter className="gap-2">
                                    <DialogClose asChild>
                                        <Button
                                            variant="secondary"
                                            className="min-h-11"
                                        >
                                            Batal
                                        </Button>
                                    </DialogClose>
                                    <Button
                                        type="submit"
                                        variant="destructive"
                                        className="min-h-11"
                                        disabled={!matches || processing}
                                        data-test="confirm-delete-account-button"
                                    >
                                        Hapus akun
                                    </Button>
                                </DialogFooter>
                            </>
                        )}
                    </Form>
                </DialogContent>
            </Dialog>
        </section>
    );
}
