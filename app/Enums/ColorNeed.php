<?php

namespace App\Enums;

/**
 * How much a PDF page needs colour, judged from the share of the page that is coloured.
 */
enum ColorNeed: string
{
    case Color = 'color';
    case Maybe = 'maybe';
    case BlackWhite = 'bw';
}
