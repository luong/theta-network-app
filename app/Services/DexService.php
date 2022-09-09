<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DexService
{
    const BINANCE_LISTING_URL = 'https://www.binance.com/en/support/announcement/c-48';

    public function getBinanceNewListing() {
        $response = Http::get(self::BINANCE_LISTING_URL);
        if (!$response->ok()) {
            return false;
        }
        if (!preg_match('/New Cryptocurrency Listing(.+?)"title":"(.+?)","type"(.+?)"releaseDate":(\\d+?)}/', $response->body(), $matches)) {
            return false;
        }
        if (empty($matches[2]) || empty($matches[4])) {
            return false;
        }

        $title = $matches[2];
        $date = date('Y-m-d H:i:s', (int)$matches[4] / 1000);

        $coin = '';
        $matched = false;

        if (preg_match('/Binance Will List (.+) \(([A-Z]+)\) in the Innovation Zone$/', $title, $matches)) {
            $matched = true;
            $coin = $matches[2];
        }

        if (!$matched && preg_match('/Binance Will List (.+) \(([A-Z]+)\)$/', $title, $matches)) {
            $matched = true;
            $coin = $matches[2];
        }

        return ['coin' => $coin, 'date' => $date];
    }
}
