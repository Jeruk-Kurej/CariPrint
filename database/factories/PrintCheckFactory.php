<?php

namespace Database\Factories;

use App\Models\PrintCheck;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrintCheck>
 */
class PrintCheckFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'shop_id' => null,
            'file_name' => fake()->word().'.pdf',
            'page_count' => 10,
            'color_pages' => [1, 4, 7],
            'suggested_bw_pages' => [4, 7],
            'estimated_savings' => 2000,
        ];
    }
}
