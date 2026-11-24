<?php

namespace Database\Factories;

use App\Enums\ContactCategory;
use App\Enums\ContactStatus;
use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'category' => ContactCategory::Other,
            'email' => fake()->safeEmail(),
            'message' => fake()->sentence(8),
            'status' => ContactStatus::New,
        ];
    }
}
