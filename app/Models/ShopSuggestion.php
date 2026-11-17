<?php

namespace App\Models;

use App\Enums\SuggestionStatus;
use Database\Factories\ShopSuggestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A visitor's request to add a print shop. Admins review it and create the shop themselves.
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string|null $maps_url
 * @property string|null $note
 * @property string|null $contact_email
 * @property SuggestionStatus $status
 * @property string|null $admin_note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'address', 'maps_url', 'note', 'contact_email', 'status', 'admin_note'])]
class ShopSuggestion extends Model
{
    /** @use HasFactory<ShopSuggestionFactory> */
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SuggestionStatus::class,
        ];
    }
}
