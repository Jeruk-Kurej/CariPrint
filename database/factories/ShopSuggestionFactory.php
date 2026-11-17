<?php

namespace Database\Factories;

use App\Enums\SuggestionStatus;
use App\Models\ShopSuggestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShopSuggestion>
 */
class ShopSuggestionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Print '.fake()->lastName(),
            'address' => fake()->streetAddress().', Surabaya',
            'status' => SuggestionStatus::Pending,
        ];
    }
}
