<x-mail::message>
# Usulan tempat printing baru

Seorang pengunjung CariPrint mengusulkan tempat berikut untuk ditambahkan.

**Nama:** {{ $suggestion->name }}

**Alamat:** {{ $suggestion->address }}

@if ($suggestion->maps_url)
**Google Maps:** {{ $suggestion->maps_url }}

@endif
@if ($suggestion->note)
**Catatan:** {{ $suggestion->note }}

@endif
@if ($suggestion->contact_email)
**Email pengusul:** {{ $suggestion->contact_email }}

@endif
<x-mail::button :url="$reviewUrl">
Tinjau di panel admin
</x-mail::button>

Usulan ini belum tampil di website. Tempat baru hanya muncul setelah admin menambahkannya.
</x-mail::message>
