<?php

namespace App\Filament;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Initials in a brand-blue circle, drawn locally. It replaces Filament's default avatar, which loads an image from
 * ui-avatars.com and so would send the user's name to a third party. The colours match the website's initials sticker
 * (--brand and --foreground in resources/css/app.css).
 */
class InitialsAvatarProvider implements AvatarProvider
{
    public function get(Model|Authenticatable $record): string
    {
        $words = preg_split('/\s+/u', trim((string) Filament::getNameForDefaultAvatar($record)), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $first = mb_substr($words[0] ?? '?', 0, 1);
        $last = count($words) > 1 ? mb_substr($words[array_key_last($words)], 0, 1) : '';
        $initials = mb_strtoupper($first.$last);

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">'
            .'<circle cx="32" cy="32" r="29" fill="#3558f0" stroke="#1d2147" stroke-width="4"/>'
            .'<text x="32" y="32" dy=".35em" text-anchor="middle" fill="#ffffff" font-family="Arial, Helvetica, sans-serif" font-weight="800" font-size="24">'
            .e($initials).'</text></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
