<?php

namespace App\Http\Requests\Settings;

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * An account linked to Google keeps the email Google gave. Letting it change would let anyone park an unused
 * address on their account, which later blocks the real owner from signing in with Google.
 */
class ProfileUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = $this->profileRules($this->user()->id);

        if ($this->user()->google_id !== null) {
            $rules['email'] = ['required', 'string', Rule::in([$this->user()->email])];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ['email.in' => 'Email akun Google tidak bisa diubah.'];
    }
}
