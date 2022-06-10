<?php

namespace App\Helpers;

class Helper
{

    public static function makeSiteAccountURL($accountId)
    {
        return Constants::SITE_URL . '/account/' . $accountId;
    }

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
        return '$' . self::formatNumber($price, $decimals, $unit);
    }

    public static function formatNumber($number, $decimals = 4, $unit = '')
    {
        if ($unit == 'B') {
            $number = $number / 1000000000;
        } else if ($unit == 'M') {
            $number = $number / 1000000;
        } else if ($unit == 'K') {
            $number = $number / 1000;
        }
        $n = number_format($number, $decimals);
        if (str_contains($n, '.')) {
            $n = rtrim(rtrim($n, '0'), '.');
        }
        return $n . $unit;
    }
}
