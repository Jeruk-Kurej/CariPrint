<x-filament-panels::page>
    {{ $this->form }}

    @if ($summary !== null)
        <x-filament::section heading="Hasil impor">
            <ul class="list-disc space-y-1 pl-5 text-sm">
                <li><strong>{{ $summary['created'] }}</strong> tempat baru masuk sebagai draf.</li>
                @if ($summary['withoutHours'] > 0)
                    <li>{{ $summary['withoutHours'] }} di antaranya tanpa jam buka dari Google. Isi manual sebelum diterbitkan.</li>
                @endif
                <li>{{ $summary['existing'] }} dilewati karena sudah ada.</li>
                @foreach ($summary['skipped'] as $reason => $count)
                    <li>{{ $count }} dilewati: {{ $reason }}.</li>
                @endforeach
            </ul>
            <p class="mt-3 text-sm">
                Periksa drafnya di
                <a class="font-medium underline" href="{{ \App\Filament\Resources\Shops\ShopResource::getUrl('index', ['filters' => ['status' => ['value' => \App\Enums\ShopStatus::Draft->value]]]) }}">Tempat printing</a>,
                lalu terbitkan yang sudah benar.
            </p>
        </x-filament::section>
    @endif
</x-filament-panels::page>
