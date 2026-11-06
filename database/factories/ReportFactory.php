<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'shop_id' => Shop::factory(),
            'message' => fake()->sentence(),
            'status' => ReportStatus::Pending,
        ];
    }
}
