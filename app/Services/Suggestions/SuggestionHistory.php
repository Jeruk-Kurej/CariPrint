<?php

namespace App\Services\Suggestions;

use App\Models\ShopSuggestion;
use Illuminate\Support\Collection;

/**
 * The suggestions one visitor sent, newest first, for the "Akunku" page. Suggestions are tied to the
 * account's email address, so no extra user column is needed.
 */
class SuggestionHistory
{
    /**
     * @return Collection<int, array{id: int, name: string, address: string, status: string, statusLabel: string, adminNote: string|null, sentAt: string}>
     */
    public function forEmail(string $email, int $limit = 20): Collection
    {
        return ShopSuggestion::query()
            ->where('contact_email', $email)
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (ShopSuggestion $suggestion) => [
                'id' => $suggestion->id,
                'name' => $suggestion->name,
                'address' => $suggestion->address,
                'status' => $suggestion->status->value,
                'statusLabel' => $suggestion->status->label(),
                'adminNote' => $suggestion->admin_note,
                'sentAt' => $suggestion->created_at?->translatedFormat('j F Y') ?? '',
            ]);
    }
}
