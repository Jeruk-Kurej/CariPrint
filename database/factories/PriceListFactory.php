<?php

namespace Database\Factories;

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceSource;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PriceList>
 */
class PriceListFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'service' => ShopService::Print,
            'paper_size' => PaperSize::A4,
            'color_mode' => ColorMode::BlackWhite,
            'price' => fake()->randomElement([300, 500, 700]),
            'unit' => PriceUnit::Page,
            'source' => PriceSource::Estimated,
        ];
    }

    public function color(): static
    {
        return $this->state(fn () => [
            'color_mode' => ColorMode::Color,
            'price' => fake()->randomElement([1000, 1500, 2000]),
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn () => ['source' => PriceSource::Verified]);
    }
}
