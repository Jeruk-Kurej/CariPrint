<x-mail::message>
# Ada pesan baru dari formulir Hubungi kami

Balas email ini untuk menjawab pengirimnya. Setelah itu tandai pesannya di panel admin.

<x-mail::panel color="sky">
**{{ $contactMessage->category->label() }}**

Dari: {{ $contactMessage->email }}

{{ $contactMessage->message }}
</x-mail::panel>

<x-mail::button :url="$reviewUrl">
Buka di panel admin
</x-mail::button>
</x-mail::message>
