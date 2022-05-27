<?php

namespace App\Helpers;

class Helper
{

    public static function makeThetaTransactionURL($transactionId)
    {
        return Constants::THETA_EXPLORER_URL . '/txs/' . $transactionId;
    }

    public static function makeThetaAccountURL($accountId)
    {
        return Constants::THETA_EXPLORER_URL . '/account/' . $accountId;
    }

    public static function makeThetaAccountLink($accountId) {
        return "<a href='" . Constants::THETA_EXPLORER_URL . "/account/{$accountId}' target='_blank'>{$accountId}</a>";
    }

    public static function formatPrice($price, $decimals = 4, $unit = '')
    {
        if ($unit == 'M') {
            $price = $price / 1000000;
        }
        $n = number_format($price, $decimals);
        if (str_contains($n, '.')) {
            $n = rtrim(rtrim($n, '0'), '.');
        }
        return '$' . $n . $unit;
    }
}
