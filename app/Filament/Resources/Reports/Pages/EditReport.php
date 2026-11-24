<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Enums\ReportStatus;
use App\Filament\Resources\Reports\ReportResource;
use App\Models\Report;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Report $report */
        $report = $this->record;

        return [
            ...$data,
            'shop_name' => $report->shop->name,
            'reporter_email' => $report->user->email,
            'category_label' => $report->category->label(),
        ];
    }

    /**
     * Status, note, and decision time are not mass assignable on the model, so a user's submission can never set them.
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->forceFill($data)->save();

        return $record;
    }

    /**
     * The time of the decision is kept the first time the report leaves "pending", and cleared if it goes back.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var Report $report */
        $report = $this->record;

        $data['resolved_at'] = $data['status'] === ReportStatus::Pending->value
            ? null
            : ($report->resolved_at ?? now());

        return $data;
    }
}
