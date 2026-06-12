<?php

namespace App\Enums;



enum FormPayment: int
{
    case DEBIT= 1;
    case CREDIT = 2;
    case PIX = 3;
    case PAY_IN_INSTALMENTS = 4;
    case MONEY = 5;

}
