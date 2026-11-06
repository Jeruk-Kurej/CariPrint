<?php

namespace Database\Factories;

use App\Enums\ShopService;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Shop>
 */
class ShopFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Print '.fake()->unique()->lastName();
        $weekday = [['open' => '08:00', 'close' => '21:00']];

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'address' => fake()->streetAddress().', CitraLand, Surabaya',
            'latitude' => fake()->randomFloat(7, -7.3000, -7.2800),
            'longitude' => fake()->randomFloat(7, 112.6200, 112.6500),
            'whatsapp' => '628'.fake()->numerify('##########'),
            'place_id' => null,
            'opening_hours' => [
                'mon' => $weekday, 'tue' => $weekday, 'wed' => $weekday,
                'thu' => $weekday, 'fri' => $weekday, 'sat' => $weekday,
                'sun' => [],
            ],
            'services' => [ShopService::Print, ShopService::Photocopy, ShopService::SpiralBinding],
            'color_page_rule' => null,
            'last_surveyed_at' => null,
        ];
    }

    public function surveyed(): static
    {
        return $this->state(fn () => [
            'last_surveyed_at' => now(),
            'color_page_rule' => 'Halaman dengan elemen warna apa pun dihitung halaman warna.',
        ]);
    }
}
