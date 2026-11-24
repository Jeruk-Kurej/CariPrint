<?php

namespace App\Services\Reports;

use App\Models\Report;
use App\Models\Shop;
use App\Models\User;

/**
 * Saves a user's report about a shop, then tells the admins. The row is stored first, so a mail failure never
 * loses the report: the admin still finds it in the panel.
 */
class ReportSubmitter
{
    public function __construct(private readonly ReportMailer $mailer) {}

    /**
     * @param  array<string, mixed>  $data  Validated fields from StoreReportRequest.
     */
    public function submit(User $user, Shop $shop, array $data): Report
    {
        $report = $user->reports()->create([...$data, 'shop_id' => $shop->id]);

        $this->mailer->notifyAdmins($report);

        return $report;
    }
}
