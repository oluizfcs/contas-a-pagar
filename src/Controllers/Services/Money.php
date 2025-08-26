<?php

namespace App\Controllers\Services;

class Money
{
    public static function formatar_centavos($centavos): string
    {
        return number_format($centavos/100, 2, ',', '.');
    }
}