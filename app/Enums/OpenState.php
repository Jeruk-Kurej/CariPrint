<?php

namespace App\Enums;

enum OpenState: string
{
    case Open = 'open';
    case ClosingSoon = 'closing_soon';
    case Closed = 'closed';
}
