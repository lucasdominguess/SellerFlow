<?php

namespace App\Enums;


enum OriginType: string
{
    case COMPRA = 'compra';
    case MANUAL = 'manual';

    case VENDA = 'venda';
}
