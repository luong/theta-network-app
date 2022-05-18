<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CoinService
{

    public function getTfuelSupply() {
        $response = Http::get('https://explorer.thetatoken.org:8443/api/supply/tfuel');
        if ($response->ok()) {
            $data = $response->json();
            return $data['circulation_supply'];
        }
        return false;
    }

    public function getCoinList() {
        $response = Http::get('https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids=bitcoin,theta-fuel,theta-token,thetadrop&price_change_percentage=24h,1y');
        if ($response->ok()) {
            $coins = [];
            $data = $response->json();
            foreach ($data as $each) {
                $name = Str::upper($each['symbol']);
                $coins[$name] = [
                    'name' => $name,
                    'image' => $each['image'],
                    'price' => $each['current_price'],
                    'market_cap' => $each['market_cap'],
                    'market_cap_rank' => $each['market_cap_rank'],
                    'volume_24h' => $each['total_volume'],
                    'price_change_24h' => $each['price_change_percentage_24h_in_currency'],
                    'price_change_1y' => $each['price_change_percentage_1y_in_currency'],
                    'price_change_ath' => $each['ath_change_percentage'],
                    'circulating_supply' => $each['circulating_supply'],
                    'ath' => $each['ath']
                ];
            }

            // Override TFUEL supply
            $tfuelSupply = $this->getTfuelSupply();
            if ($tfuelSupply !== false) {
                $coins['TFUEL']['circulating_supply'] = $tfuelSupply;
            }

            return $coins;
        }
        return false;
    }
}
