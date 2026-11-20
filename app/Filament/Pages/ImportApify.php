<?php

namespace App\Filament\Pages;

use App\Services\Survey\ApifyDraftImporter;
use App\Services\Survey\SurveyArea;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;

/**
 * Adds the places from an Apify Google Maps export as drafts. The file is only read for this request and is
 * never kept; the admin checks the drafts under "Tempat printing" before publishing them.
 *
 * @property-read Schema $form
 */
class ImportApify extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static ?string $navigationLabel = 'Impor dari Apify';

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'Impor dari Apify';

    protected string $view = 'filament.pages.import-apify';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    /**
     * @var array{created: int, existing: int, withoutHours: int, skipped: array<string, int>}|null
     */
    public ?array $summary = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        $maxRadius = config('cariprint.import.max_radius_km');

        return $schema
            ->components([
                Form::make([
                    FileUpload::make('file')->label('Berkas CSV dari Apify')->required()
                        ->multiple()->maxFiles(10)
                        ->storeFiles(false)
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                        ->maxSize(10240)
                        ->helperText('Boleh beberapa berkas sekaligus. Berkas tidak disimpan, dan semua tempat masuk sebagai draf.'),
                    TextInput::make('radius')->label('Batasi radius dari CitraLand (km), opsional')->numeric()
                        ->minValue(1)->maxValue($maxRadius)
                        ->placeholder('Kosongkan untuk seluruh Surabaya')
                        ->helperText("Isi hanya kalau ingin membatasi ke sekitar kampus, maksimal {$maxRadius} km."),
                ])
                    ->livewireSubmitHandler('import')
                    ->footer([
                        Actions::make([
                            Action::make('import')->label('Impor sebagai draf')->submit('import'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function import(ApifyDraftImporter $importer): void
    {
        $data = $this->form->getState();
        $paths = collect(Arr::wrap($data['file']))
            ->filter(fn ($file) => $file instanceof TemporaryUploadedFile)
            ->map(fn (TemporaryUploadedFile $file) => (string) $file->getRealPath())
            ->values();

        if ($paths->isEmpty()) {
            return;
        }

        try {
            $this->summary = $importer->importAll(array_values($paths->all()), $this->area($data['radius'] ?? null));
        } catch (InvalidArgumentException|RuntimeException $exception) {
            Notification::make()->danger()->title('Berkas tidak bisa diimpor')->body($exception->getMessage())->send();

            return;
        }

        collect(Arr::wrap($data['file']))->each(fn ($file) => $file instanceof TemporaryUploadedFile ? $file->delete() : null);
        $this->form->fill(['radius' => $data['radius'] ?? null]);

        Notification::make()->success()->title("{$this->summary['created']} tempat ditambahkan sebagai draf")->send();
    }

    /**
     * Without a radius the whole city is accepted; with one, only places near the middle of CitraLand.
     */
    private function area(mixed $radius): SurveyArea
    {
        if (blank($radius)) {
            return SurveyArea::box(config('cariprint.city_area'), 'Surabaya');
        }

        $box = config('cariprint.area');

        return SurveyArea::circle(
            ($box['min_latitude'] + $box['max_latitude']) / 2,
            ($box['min_longitude'] + $box['max_longitude']) / 2,
            (float) $radius,
        );
    }
}
