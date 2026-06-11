<?php

namespace App\Enums;



enum TipoStock: string
{
    case ENTRADA = 'entrada';
    case SAIDA = 'saida';
    case AJUSTE='ajuste';


}
