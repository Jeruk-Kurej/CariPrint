<?php

namespace App\Enums;

enum ShopService: string
{
    case Print = 'print';
    case Photocopy = 'photocopy';
    case Scan = 'scan';
    case Laminating = 'laminating';
    case SpiralBinding = 'spiral_binding';
    case TapeBinding = 'tape_binding';
    case HardcoverBinding = 'hardcover_binding';
}
