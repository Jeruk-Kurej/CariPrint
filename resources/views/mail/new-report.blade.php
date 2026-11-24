<x-mail::message>
# Ada laporan data baru

Seorang pengguna CariPrint melaporkan data yang keliru. Periksa tempatnya, perbaiki kalau benar, lalu putuskan di panel admin.

<x-mail::panel color="sky">
**{{ $report->shop->name }}**

Jenis masalah: {{ $report->category->label() }}

Pesan pelapor: {{ $report->message }}

Pelapor: {{ $report->user->email }}
</x-mail::panel>

<x-mail::button :url="$reviewUrl">
Tinjau laporan
</x-mail::button>
</x-mail::message>
