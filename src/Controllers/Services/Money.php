<?php

namespace App\Controllers\Services;

class Money
{
    public static function centavos_para_reais(int|null $centavos): string
    {
        return number_format($centavos/100, 2, ',', '.');
    }

    public static function reais_para_centavos(string $reais): int
    {
        if(!(strlen($reais) > 0)) {
            return 0;
        }
        
        preg_match_all('/[0-9]/', $reais, $matches);
        return implode('', $matches[0]);
    }
}