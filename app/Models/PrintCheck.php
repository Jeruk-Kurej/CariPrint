<?php

namespace App\Models;

use Database\Factories\PrintCheckFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Saved Smart File Check result for a logged-in user.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $shop_id
 * @property string $file_name
 * @property int $page_count
 * @property list<int> $color_pages
 * @property list<int> $suggested_bw_pages
 * @property int $estimated_savings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['shop_id', 'file_name', 'page_count', 'color_pages', 'suggested_bw_pages', 'estimated_savings'])]
class PrintCheck extends Model
{
    /** @use HasFactory<PrintCheckFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
            'page_count' => 'integer',
            'color_pages' => 'array',
            'suggested_bw_pages' => 'array',
            'estimated_savings' => 'integer',
        ];
    }
}
