<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CoinService
{

    const THETA_API_URL = 'https://explorer.thetatoken.org:8443';
    const COINGECKO_API_URL = 'https://api.coingecko.com';
    const CMC_API_KEY = '0f5696f0-e3a6-4468-82d6-498434266ab8';
    const CMC_API_URL = 'https://pro-api.coinmarketcap.com';

    public function getThetaMarketingData() {
        $response = Http::get('https://marketing-api.thetatoken.org/v1/nodes/locations/optimized');
        if ($response->ok()) {
            $data = $response->json();
            return [
                'edge_nodes' => $data['totals']['en'],
                'guardian_nodes' => $data['totals']['gn'],
                'validators' => $data['totals']['validator']
            ];
        }
        return false;
    }

    public function getTfuelSupply() {
        $response = Http::get(self::THETA_API_URL . '/api/supply/tfuel');
        if ($response->ok()) {
            $data = $response->json();
            return $data['circulation_supply'];
        }
        return false;
    }

    public function getThetaStats() {
        $thetaTotalStakes = null;
        $thetaStakedNodes = null;
        $thetaPrice = null;
        $thetaMarketCap = null;
        $thetaVolume24h = null;
        $tfuelSupply = null;
        $tfuelTotalStakes = null;
        $tfuelStakedNodes = null;
        $tfuelPrice = null;
        $tfuelMarketCap = null;
        $tfuelVolume24h = null;
        $onchainWallets = null;
        $activeWallets = null;

        // theta supply
        $response = Http::get(self::THETA_API_URL . '/api/supply/theta');
        if ($response->ok()) {
            $data = $response->json();
            $thetaSupply = $data['circulation_supply'];
        } else {
            return false;
        }

        // theta stakes
        $response = Http::get(self::THETA_API_URL . '/api/stake/totalAmount?type=theta');
        if ($response->ok()) {
            $data = $response->json();
            $thetaTotalStakes = substr($data['body']['totalAmount'], 0, -18);
            $thetaStakedNodes = $data['body']['totalNodes'];
        } else {
            return false;
        }

        // tfuel supply
        $tfuelSupply = $this->getTfuelSupply();
        if ($tfuelSupply === false) {
            return false;
        }

        // tfuel stakes
        $response = Http::get(self::THETA_API_URL . '/api/stake/totalAmount?type=tfuel');
        if ($response->ok()) {
            $data = $response->json();
            $tfuelTotalStakes = substr($data['body']['totalAmount'], 0, -18);
            $tfuelStakedNodes = $data['body']['totalNodes'];
        } else {
            return false;
        }

        // onchain wallets
        $response = Http::get(self::THETA_API_URL . '/api/account/total/number');
        if ($response->ok()) {
            $data = $response->json();
            $onchainWallets = $data['total_number_account'];
        } else {
            return false;
        }

        // active wallets
        $response = Http::get(self::THETA_API_URL . '/api/activeAccount/latest');
        if ($response->ok()) {
            $data = $response->json();
            $activeWallets = $data['body']['amount'];
        } else {
            return false;
        }

        // prices
        $response = Http::get(self::THETA_API_URL . '/api/price/all');
        if ($response->ok()) {
            $data = $response->json();
            foreach ($data['body'] as $each) {
                if ($each['_id'] == 'THETA') {
                    $thetaPrice = $each['price'];
                    $thetaMarketCap = $each['market_cap'];
                    $thetaVolume24h = $each['volume_24h'];
                } else if ($each['_id'] == 'TFUEL') {
                    $tfuelPrice = $each['price'];
                    $tfuelMarketCap = $each['market_cap'];
                    $tfuelVolume24h = $each['volume_24h'];
                }
            }
        } else {
            return false;
        }

        return [
            'theta' => ['price' => $thetaPrice, 'market_cap' => $thetaMarketCap, 'volume_24h' => $thetaVolume24h, 'supply' => $thetaSupply, 'total_stakes' => $thetaTotalStakes, 'staked_nodes' => $thetaStakedNodes],
            'tfuel' => ['price' => $tfuelPrice, 'market_cap' => $tfuelMarketCap, 'volume_24h' => $tfuelVolume24h, 'supply' => $tfuelSupply, 'total_stakes' => $tfuelTotalStakes, 'staked_nodes' => $tfuelStakedNodes],
            'network' => ['onchain_wallets' => $onchainWallets, 'active_wallets' => $activeWallets]
        ];
    }

    public function getCoinList() {
        $coinsFromCMC = Cache::get('coins_cmc');
        if (empty($coinsFromCMC)) {
            $coinsFromCMC = $this->getCoinListFromCMC();
            Cache::put('coins_cmc', $coinsFromCMC, now()->addMinutes(30));
        }
        $coins = $this->getCoinListFromCoingecko();
        if ($coins === false) {
            return false;
        }

        $coins['TFUEL']['circulating_supply'] = $this->getTfuelSupply();
        $coins['TFUEL']['market_cap'] = $coinsFromCMC['TFUEL']['market_cap'];
        $coins['TFUEL']['market_cap_rank'] = $coinsFromCMC['TFUEL']['market_cap_rank'];
        $coins['TDROP']['market_cap'] = $coinsFromCMC['TDROP']['market_cap'];
        $coins['TDROP']['market_cap_rank'] = $coinsFromCMC['TDROP']['market_cap_rank'];

        usort($coins, function($coin1, $coin2) {
            if ($coin1['price'] < $coin2['price']) {
                return 1;
            } else {
                return -1;
            }
        });

        return $coins;
    }

    public function getCoinListFromCoingecko() {
        $response = Http::get(self::COINGECKO_API_URL . '/api/v3/coins/markets?vs_currency=usd&ids=bitcoin,theta-fuel,theta-token,thetadrop&price_change_percentage=24h,1y');
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
            return $coins;
        }
        return false;
    }

    private function getCoinListFromCMC() {
        $response = Http::get(self::CMC_API_URL . '/v2/cryptocurrency/quotes/latest?CMC_PRO_API_KEY=' . self::CMC_API_KEY . '&symbol=BTC,THETA,TFUEL,TDROP');
        if ($response->ok()) {
            $coins = [];
            $data = $response->json();
            foreach ($data['data'] as $name => $each) {
                $details = $each[0];
                $coins[$name] = [
                    'name' => $name,
                    'image' => '',
                    'price' => $details['quote']['USD']['price'],
                    'market_cap' => $details['quote']['USD']['market_cap'] > 0 ? $details['quote']['USD']['market_cap'] : $details['self_reported_market_cap'],
                    'market_cap_rank' => $details['cmc_rank'],
                    'volume_24h' => $details['quote']['USD']['volume_24h'],
                    'price_change_24h' => $details['quote']['USD']['percent_change_24h'],
                    'price_change_1y' => '',
                    'price_change_ath' => '',
                    'circulating_supply' => $details['circulating_supply'],
                    'ath' => ''
                ];
            }
            return $coins;
        }
        return false;
    }
}
