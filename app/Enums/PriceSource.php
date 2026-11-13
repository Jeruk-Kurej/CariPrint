<?php

namespace App\Enums;

enum PriceSource: string
{
    case Verified = 'verified';
    case Estimated = 'estimated';
    case Demo = 'demo';
}
