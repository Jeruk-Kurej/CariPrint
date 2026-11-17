<x-mail::message>
# Terima kasih, usulanmu sudah masuk

Kami sudah menerima usulan tempat printing darimu. Sekarang usulan itu menunggu persetujuan admin CariPrint.

<x-mail::panel color="yellow">
**{{ $suggestion->name }}**

{{ $suggestion->address }}

Status: menunggu persetujuan
</x-mail::panel>

Admin akan mengecek datanya dulu. Kalau sudah diputuskan, kami kirim kabarnya ke email ini. Tempat baru tampil di CariPrint setelah admin menambahkannya.

<x-mail::button :url="route('directory.index')">
Lihat tempat printing
</x-mail::button>
</x-mail::message>
