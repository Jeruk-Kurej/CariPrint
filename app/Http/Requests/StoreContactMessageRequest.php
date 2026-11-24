<?php

namespace App\Http\Requests;

use App\Enums\ContactCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactMessageRequest extends FormRequest
{
    /**
     * "website" is a hidden trap field: people never see it, so anything typed there comes from a bot.
     * A signed-in user's email comes from the account, so the email field only matters for guests.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', Rule::enum(ContactCategory::class)],
            'email' => [$this->user() === null ? 'required' : 'nullable', 'string', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:1000'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category.required' => 'Pilih jenis pesannya dulu.',
            'category.enum' => 'Jenis pesan yang kamu pilih tidak dikenal.',
            'email.required' => 'Isi emailmu supaya kami bisa membalas.',
            'message.required' => 'Tulis pesannya dulu.',
            'message.min' => 'Ceritakan sedikit lebih jelas, minimal :min karakter.',
            'message.max' => 'Pesannya terlalu panjang, maksimal :max karakter.',
        ];
    }

    /**
     * The address the team replies to: the account's own for a signed-in user, the typed one for a guest.
     */
    public function replyEmail(): string
    {
        return $this->user()->email ?? $this->string('email')->toString();
    }
}
