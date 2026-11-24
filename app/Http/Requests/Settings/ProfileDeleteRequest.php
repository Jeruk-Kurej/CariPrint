<?php

namespace App\Http\Requests\Settings;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Deleting an account is confirmed by typing the account's own email address. Most accounts sign in with Google
 * and never see a password, so a password check would lock them out. Admins are created by command and cannot
 * delete themselves here.
 */
class ProfileDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! $this->user()->isAdmin();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                fn (string $attribute, mixed $value, Closure $fail) => $this->matchesAccountEmail((string) $value)
                    ? null
                    : $fail('Email yang kamu ketik tidak sama dengan emailmu.'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ['email.required' => 'Ketik emailmu untuk mengonfirmasi.'];
    }

    private function matchesAccountEmail(string $typed): bool
    {
        return mb_strtolower(trim($typed)) === mb_strtolower($this->user()->email);
    }
}
