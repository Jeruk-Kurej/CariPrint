<?php

namespace App\Models;

use App\Enums\ContactCategory;
use App\Enums\ContactStatus;
use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A message sent through the "Hubungi kami" form, by a signed-in user or by a guest who left an email address.
 * Status and the admin's note are set in the admin panel, so they are not mass assignable.
 *
 * @property int $id
 * @property int|null $user_id
 * @property ContactCategory $category
 * @property string $email
 * @property string $message
 * @property ContactStatus $status
 * @property string|null $admin_note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['category', 'email', 'message'])]
class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'new',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ContactCategory::class,
            'status' => ContactStatus::class,
        ];
    }
}
