@php($accepted = $suggestion->status === \App\Enums\SuggestionStatus::Accepted)
<x-mail::message>
@if ($accepted)
# Usulanmu diterima

Kabar baik: admin CariPrint menerima usulan tempat printing darimu.

<x-mail::panel color="mint">
**{{ $suggestion->name }}**

{{ $suggestion->address }}

Status: diterima
@if ($suggestion->admin_note)

Catatan admin: {{ $suggestion->admin_note }}
@endif
</x-mail::panel>

Tempat ini akan masuk ke daftar CariPrint. Terima kasih sudah membantu mahasiswa lain menemukan tempat print.

<x-mail::button :url="route('directory.index')">
Lihat tempat printing
</x-mail::button>
@else
# Usulanmu belum bisa kami tambahkan

Terima kasih sudah mengusulkan. Setelah dicek, admin CariPrint belum bisa menambahkan tempat ini.

<x-mail::panel color="pink">
**{{ $suggestion->name }}**

{{ $suggestion->address }}

Status: ditolak
@if ($suggestion->admin_note)

Alasan admin: {{ $suggestion->admin_note }}
@endif
</x-mail::panel>

Kamu boleh mengusulkan tempat lain, atau mengirim ulang dengan data yang lebih lengkap.

<x-mail::button :url="route('suggestions.create')">
Usulkan tempat lain
</x-mail::button>
@endif
</x-mail::message>
