<?php

namespace App\Services\Reports;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * What one user reported, newest first, for the "Akunku" page, and whether a report on a shop is still waiting.
 */
class ReportHistory
{
    /**
     * @return Collection<int, array{id: int, shopName: string, shopSlug: string, category: string, categoryLabel: string, message: string, status: string, statusLabel: string, adminNote: string|null, sentAt: string}>
     */
    public function forUser(User $user, int $limit = 20): Collection
    {
        return $user->reports()
            ->with('shop')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (Report $report) => [
                'id' => $report->id,
                'shopName' => $report->shop->name,
                'shopSlug' => $report->shop->slug,
                'category' => $report->category->value,
                'categoryLabel' => $report->category->label(),
                'message' => $report->message,
                'status' => $report->status->value,
                'statusLabel' => $report->status->label(),
                'adminNote' => $report->admin_note,
                'sentAt' => $report->created_at?->translatedFormat('j F Y') ?? '',
            ]);
    }

    public function hasPendingFor(User $user, Shop $shop): bool
    {
        return $user->reports()
            ->where('shop_id', $shop->id)
            ->where('status', ReportStatus::Pending->value)
            ->exists();
    }
}
