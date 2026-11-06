<?php

namespace App\Models;

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceSource;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use Database\Factories\PriceListFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Price of one service at one shop, stored as whole Rupiah.
 *
 * @property int $id
 * @property int $shop_id
 * @property ShopService $service
 * @property PaperSize|null $paper_size
 * @property ColorMode|null $color_mode
 * @property int $price
 * @property PriceUnit $unit
 * @property PriceSource $source
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['service', 'paper_size', 'color_mode', 'price', 'unit', 'source'])]
class PriceList extends Model
{
    /** @use HasFactory<PriceListFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Shop, $this>
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'service' => ShopService::class,
            'paper_size' => PaperSize::class,
            'color_mode' => ColorMode::class,
            'price' => 'integer',
            'unit' => PriceUnit::class,
            'source' => PriceSource::class,
        ];
    }
}
