<?php

namespace App\Enums;


enum OriginType: string
{
    case COMPRA = 'compra';
    case AJUSTE_MANUAL = 'ajuste_manual';

    case VENDA = 'venda';
}
