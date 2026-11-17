<x-mail::message>
# Ada usulan tempat printing baru

Seorang pengunjung CariPrint mengusulkan tempat berikut. Tinjau dan putuskan di panel admin.

<x-mail::panel color="sky">
**{{ $suggestion->name }}**

{{ $suggestion->address }}
@if ($suggestion->maps_url)

Google Maps: {{ $suggestion->maps_url }}
@endif
@if ($suggestion->note)

Catatan pengusul: {{ $suggestion->note }}
@endif
@if ($suggestion->contact_email)

Email pengusul: {{ $suggestion->contact_email }}
@endif
</x-mail::panel>

<x-mail::button :url="$reviewUrl">
Tinjau usulan
</x-mail::button>

Usulan ini belum tampil di website. Pengusul otomatis mendapat email saat kamu menerima atau menolaknya.
</x-mail::message>
