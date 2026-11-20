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
        $this->form->fill(['radius' => config('cariprint.import.default_radius_km')]);
    }

    public function form(Schema $schema): Schema
    {
        $maxRadius = config('cariprint.import.max_radius_km');

        return $schema
            ->components([
                Form::make([
                    FileUpload::make('file')->label('Berkas CSV dari Apify')->required()
                        ->storeFiles(false)
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                        ->maxSize(10240)
                        ->helperText('Export dataset Apify Google Maps Scraper, format CSV. Berkas tidak disimpan.'),
                    TextInput::make('radius')->label('Radius dari CitraLand (km)')->required()->numeric()
                        ->minValue(1)->maxValue($maxRadius)
                        ->helperText("Hanya tempat dalam radius ini yang diimpor, maksimal {$maxRadius} km."),
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
        $file = Arr::first(Arr::wrap($data['file']));

        if (! $file instanceof TemporaryUploadedFile) {
            return;
        }

        $box = config('cariprint.area');
        $area = SurveyArea::circle(
            ($box['min_latitude'] + $box['max_latitude']) / 2,
            ($box['min_longitude'] + $box['max_longitude']) / 2,
            (float) $data['radius'],
        );

        try {
            $this->summary = $importer->import((string) $file->getRealPath(), $area);
        } catch (InvalidArgumentException|RuntimeException $exception) {
            Notification::make()->danger()->title('Berkas tidak bisa diimpor')->body($exception->getMessage())->send();

            return;
        }

        $file->delete();
        $this->form->fill(['radius' => $data['radius']]);

        Notification::make()->success()->title("{$this->summary['created']} tempat ditambahkan sebagai draf")->send();
    }
}
