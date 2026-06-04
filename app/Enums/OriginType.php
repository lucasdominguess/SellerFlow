<?php

namespace App\Enums;


enum OriginType: string
{
    case COMPRA = 'compra';
    case MANUAL = 'ajuste_manual';

    case VENDA = 'venda';
}
