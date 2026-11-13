<?php

namespace App\Models;

use App\Enums\ShopService;
use Database\Factories\ShopFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $address
 * @property float $latitude
 * @property float $longitude
 * @property string|null $whatsapp
 * @property string|null $place_id
 * @property array<string, list<array{open: string, close: string}>> $opening_hours
 * @property Collection<int, ShopService> $services
 * @property string|null $color_page_rule
 * @property Carbon|null $last_surveyed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'name', 'slug', 'address', 'latitude', 'longitude', 'whatsapp', 'place_id',
    'opening_hours', 'services', 'color_page_rule', 'last_surveyed_at',
])]
class Shop extends Model
{
    /** @use HasFactory<ShopFactory> */
    use HasFactory;

    /**
     * @return HasMany<PriceList, $this>
     */
    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }

    /**
     * True when any loaded price is sample data, so pages can tell visitors the numbers are not real.
     */
    /**
     * @return HasMany<Report, $this>
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'opening_hours' => 'array',
            'services' => AsEnumCollection::of(ShopService::class),
            'last_surveyed_at' => 'datetime',
        ];
    }
}
