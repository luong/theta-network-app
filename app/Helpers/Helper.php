<?php

namespace App\Helpers;

class Helper
{

    public static function formatPrice($price, $decimals = 4)
    {
        $n = number_format($price, $decimals);
        if (str_contains($n, '.')) {
            $n = rtrim(rtrim($n, '0'), '.');
        }
        return '$' . $n;
    }
}
