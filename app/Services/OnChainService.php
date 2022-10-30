<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OnChainService
{

    public function getTVL() {
        $response = Http::get(Constants::DL_API_URL . '/charts/theta');
        if (!$response->ok()) {
            Log::channel('db')->error('Request failed: llama/charts/theta');
            return false;
        }
        $data = $response->json();
        $todayData = round($data[count($data) - 1]['totalLiquidityUSD']);
        $prevData = round($data[count($data) - 2]['totalLiquidityUSD']);
        return [
            'current_value' => $todayData,
            'change_24h' => round(($todayData - $prevData) / $prevData, 4)
        ];
    }

    public function getThetaMarketingData() {
        $response = Http::get(Constants::THETA_MARKETING_API_URL . '/v1/nodes/locations/optimized');
        if ($response->ok()) {
            $data = $response->json();
            return [
                'edge_nodes' => $data['totals']['en'],
                'guardian_nodes' => $data['totals']['gn'],
                'validators' => $data['totals']['validator']
            ];
        } else {
            Log::channel('db')->error('Request failed: theta/v1/nodes/locations/optimized');
        }
        return false;
    }

    public function getTfuelSupply() {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/supply/tfuel');
        if ($response->ok()) {
            $data = $response->json();
            return $data['circulation_supply'];
        } else {
            Log::channel('db')->error('Request failed: theta/api/supply/tfuel');
        }
        return false;
    }

    public function getTdropSupply() {
        $response = $this->getContractSummary(Constants::TDROP_CONTRACT_ID);
        if ($response !== false) {
            return round($response['max_total_supply'] / Constants::THETA_WEI, 0);
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
        $tdropSupply = null;
        $tdropTotalStakes = null;

        // theta supply
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/supply/theta');
        if ($response->ok()) {
            $data = $response->json();
            $thetaSupply = $data['circulation_supply'];
        } else {
            Log::channel('db')->error('Request failed: theta/api/supply/theta');
            return false;
        }

        // theta stakes
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/stake/totalAmount?type=theta');
        if ($response->ok()) {
            $data = $response->json();
            $thetaTotalStakes = substr($data['body']['totalAmount'], 0, -18);
            $thetaStakedNodes = $data['body']['totalNodes'];
        } else {
            Log::channel('db')->error('Request failed: theta/api/stake/totalAmount?type=theta');
            return false;
        }

        // tfuel supply
        $tfuelSupply = $this->getTfuelSupply();
        if ($tfuelSupply === false) {
            return false;
        }

        // tfuel stakes
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/stake/totalAmount?type=tfuel');
        if ($response->ok()) {
            $data = $response->json();
            $tfuelTotalStakes = substr($data['body']['totalAmount'], 0, -18);
            $tfuelStakedNodes = $data['body']['totalNodes'];
        } else {
            Log::channel('db')->error('Request failed: theta/api/stake/totalAmount?type=tfuel');
            return false;
        }

        // tdrop supply
        $tdropSupply = $this->getTdropSupply();

        // tdrop total stakes
        $tdropContract = resolve(TdropContract::class);
        $tdropTotalStakes = $tdropContract->getBalance(Constants::TDROP_STAKING_ADDRESS);

        // onchain wallets
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/account/total/number');
        if ($response->ok()) {
            $data = $response->json();
            $onchainWallets = $data['total_number_account'];
        } else {
            Log::channel('db')->error('Request failed: theta/api/account/total/number');
            return false;
        }

        // active wallets
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/activeAccount/latest');
        if ($response->ok()) {
            $data = $response->json();
            $activeWallets = $data['body']['amount'];
        } else {
            Log::channel('db')->error('Request failed: theta/api/activeAccount/latest');
            return false;
        }

        // prices
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/price/all');
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
            Log::channel('db')->error('Request failed: theta/api/price/all');
            return false;
        }

        return [
            'theta' => ['price' => $thetaPrice, 'market_cap' => $thetaMarketCap, 'volume_24h' => $thetaVolume24h, 'supply' => $thetaSupply, 'total_stakes' => $thetaTotalStakes, 'staked_nodes' => $thetaStakedNodes],
            'tfuel' => ['price' => $tfuelPrice, 'market_cap' => $tfuelMarketCap, 'volume_24h' => $tfuelVolume24h, 'supply' => $tfuelSupply, 'total_stakes' => $tfuelTotalStakes, 'staked_nodes' => $tfuelStakedNodes],
            'tdrop' => ['price' => false, 'market_cap' => false, 'volume_24h' => false, 'supply' => $tdropSupply, 'total_stakes' => $tdropTotalStakes, 'staked_nodes' => false],
            'network' => ['onchain_wallets' => $onchainWallets, 'active_wallets' => $activeWallets]
        ];
    }

    public function getCoinList() {
        $coinsFromCMC = Cache::get('coins_cmc');
        if (empty($coinsFromCMC)) {
            $coinsFromCMC = $this->getCoinListFromCMC();
            Cache::put('coins_cmc', $coinsFromCMC, now()->addMinutes(5));
        }

        $coins = Cache::get('coins_gko');
        if (empty($coins)) {
            $coins = $this->getCoinListFromCoingecko();
            if ($coins === false) {
                return false;
            }
            Cache::put('coins_gko', $coins, now()->addMinutes(2));
        }

        $coins['THETA']['market_cap'] = $coinsFromCMC['THETA']['market_cap'];
        $coins['THETA']['market_cap_rank'] = $coinsFromCMC['THETA']['market_cap_rank'];
        $coins['TFUEL']['circulating_supply'] = $this->getTfuelSupply();
        $coins['TFUEL']['market_cap'] = $coinsFromCMC['TFUEL']['market_cap'];
        $coins['TFUEL']['market_cap_rank'] = $coinsFromCMC['TFUEL']['market_cap_rank'];
        $coins['TDROP']['circulating_supply'] = $this->getTdropSupply();
        $coins['TDROP']['market_cap'] = $coinsFromCMC['TDROP']['market_cap'];
        $coins['TDROP']['market_cap_rank'] = $coinsFromCMC['TDROP']['market_cap_rank'];

        $binancePrices = $this->getPricesFromBinance();
        $coins['BTC']['price'] = $binancePrices['BTC'];
        $coins['THETA']['price'] = $binancePrices['THETA'];
        $coins['TFUEL']['price'] = $binancePrices['TFUEL'];

        uasort($coins, function($coin1, $coin2) {
            if ($coin1['price'] < $coin2['price']) {
                return 1;
            } else {
                return -1;
            }
        });

        return $coins;
    }

    public function getCoinListFromCoingecko() {
        $response = Http::get(Constants::COINGECKO_API_URL . '/api/v3/coins/markets?vs_currency=usd&ids=bitcoin,theta-fuel,theta-token,thetadrop&price_change_percentage=24h,1y');
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
        } else {
            Log::channel('db')->error('Request failed: coingecko/api/v3/coins/markets');
        }
        return false;
    }

    private function getCoinListFromCMC() {
        $response = Http::get(Constants::CMC_API_URL . '/v2/cryptocurrency/quotes/latest?CMC_PRO_API_KEY=' . Constants::CMC_API_KEY . '&symbol=BTC,THETA,TFUEL,TDROP');
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
                    'volume_change_24h' => $details['quote']['USD']['volume_change_24h'],
                    'price_change_24h' => $details['quote']['USD']['percent_change_24h'],
                    'price_change_1y' => '',
                    'price_change_ath' => '',
                    'circulating_supply' => $details['circulating_supply'],
                    'ath' => ''
                ];
            }
            return $coins;
        } else {
            Log::channel('db')->error('Request failed: cmc/v2/cryptocurrency/quotes/latest');
        }
        return false;
    }

    public function getAccount($id, $useTdrop = false)
    {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/account/' . $id);
        if ($response->ok()) {
            $data = $response->json();
            $theta = round($data['body']['balance']['thetawei'] / Constants::THETA_WEI, 2);
            $tfuel = round($data['body']['balance']['tfuelwei'] / Constants::THETA_WEI, 2);

            $tdrop = 0;
            if ($useTdrop) {
                $tdropContract = resolve(TdropContract::class);
                $tdropBalance = $tdropContract->getBalance($id);
                if ($tdropBalance !== false) {
                    $tdrop = round($tdropBalance, 2);
                }
            }

            $thetaService = resolve(ThetaService::class);
            $stakings = $thetaService->getStakingsByAccountId($id);

            return [
                'id' => $id,
                'balance' => [
                    'theta' => $theta,
                    'tfuel' => $tfuel,
                    'tdrop' => $tdrop
                ],
                'staking' => [
                    'theta' => $stakings['theta'],
                    'tfuel' => $stakings['tfuel'],
                    'tdrop' => $stakings['tdrop']
                ]
            ];

        } else {
            Log::channel('db')->error('Request failed: theta/api/account');
        }
        return false;
    }

    public function getAccountDetails($id, $useTdrop = false)
    {
        $account = $this->getAccount($id, $useTdrop);
        $account['transactions'] = $this->getAccountTransactions($id);
        $account['stakes'] = $this->getAccountStakes($id);
        return $account;
    }

    public function getAccountTransactions($accountId) {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/accounttx/' . $accountId . '?type=-1&pageNumber=1&limitNumber=100&isEqualType=true&types=["2","9","10"]');
        if ($response->ok()) {
            $coins = $this->getCoinList();
            $transactions = [];

            $data = $response->json();
            foreach ($data['body'] as $transaction) {
                $txn = [];
                $usd = 0;
                if ($transaction['type'] == 2) {
                    $theta = round($transaction['data']['outputs'][0]['coins']['thetawei'] / Constants::THETA_WEI, 2);
                    $tfuel = round($transaction['data']['outputs'][0]['coins']['tfuelwei'] / Constants::THETA_WEI, 2);

                    if ($theta > 0) {
                        $usd = round($theta * $coins['THETA']['price'], 2);
                        $txn = [
                            'id' => $transaction['_id'],
                            'type' => 'transfer',
                            'date' => date('Y-m-d H:i', $transaction['timestamp']),
                            'from' => $transaction['data']['inputs'][0]['address'],
                            'to' => $transaction['data']['outputs'][0]['address'],
                            'amount' => number_format($theta) . ' $theta (' . Helper::formatPrice($usd, 0) . ')',
                            'coins' => $theta,
                            'currency' => 'theta',
                            'usd' => $usd
                        ];

                    } else {
                        $usd = round($tfuel * $coins['TFUEL']['price'], 2);
                        $txn = [
                            'id' => $transaction['_id'],
                            'type' => 'transfer',
                            'date' => date('Y-m-d H:i', $transaction['timestamp']),
                            'from' => $transaction['data']['inputs'][0]['address'],
                            'to' => $transaction['data']['outputs'][0]['address'],
                            'amount' => number_format($tfuel) . ' $tfuel (' . Helper::formatPrice($usd, 0) . ')',
                            'coins' => $tfuel,
                            'currency' => 'tfuel',
                            'usd' => $usd
                        ];
                    }

                } else if ($transaction['type'] == 10) { // stake
                    $theta = round($transaction['data']['source']['coins']['thetawei'] / Constants::THETA_WEI, 2);
                    $tfuel = round($transaction['data']['source']['coins']['tfuelwei'] / Constants::THETA_WEI, 2);

                    if ($theta > 0) {
                        $usd = round($theta * $coins['THETA']['price'], 2);
                        $txn = [
                            'id' => $transaction['_id'],
                            'type' => 'stake',
                            'date' => date('Y-m-d H:i', $transaction['timestamp']),
                            'from' => $transaction['data']['source']['address'],
                            'to' => $transaction['data']['holder']['address'],
                            'amount' => number_format($theta) . ' $theta (' . Helper::formatPrice($usd, 0) . ')',
                            'coins' => $theta,
                            'currency' => 'theta',
                            'usd' => $usd
                        ];

                    } else {
                        $usd = round($tfuel * $coins['TFUEL']['price'], 2);
                        $txn = [
                            'id' => $transaction['_id'],
                            'type' => 'stake',
                            'date' => date('Y-m-d H:i', $transaction['timestamp']),
                            'from' => $transaction['data']['source']['address'],
                            'to' => $transaction['data']['holder']['address'],
                            'amount' => number_format($tfuel) . ' $tfuel (' . Helper::formatPrice($usd, 0) . ')',
                            'coins' => $tfuel,
                            'currency' => 'tfuel',
                            'usd' => $usd
                        ];
                    }

                } else if ($transaction['type'] == 9) {
                    $txn = [
                        'id' => $transaction['_id'],
                        'type' => 'unstake',
                        'date' => date('Y-m-d H:i', $transaction['timestamp']),
                        'from' => $transaction['data']['holder']['address'],
                        'to' => $transaction['data']['source']['address'],
                        'amount' => '0',
                        'coins' => '0',
                        'currency' => 'theta',
                        'usd' => 0
                    ];
                }

                $transactions[] = $txn;
            }

            return $transactions;

        } else {
            Log::channel('db')->error('Request failed: theta/api/accounttx');
        }
        return false;
    }

    public function getAccountStakes($accountId) {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/stake/' . $accountId . '?types[]=vcp&types[]=gcp&types[]=eenp');
        if ($response->ok()) {
            $stakes = [];
            $data = $response->json();
            $list = $data['body']['holderRecords'];
            $role = 'holder';
            if (empty($data['body']['holderRecords'])) {
                $list = $data['body']['sourceRecords'];
                $role = 'source';
            }
            foreach ($list as $each) {
                if ($each['type'] == 'gcp') {
                    $theta = round($each['amount'] / Constants::THETA_WEI, 2);
                    $stakes[] = [
                        'id' => $each['_id'],
                        'role' => $role,
                        'type' => 'gcp',
                        'holder' => $each['holder'],
                        'source' => $each['source'],
                        'coins' => $theta,
                        'currency' => 'theta',
                        'status' => $each['withdrawn'] ? 'unstaking' : 'staking',
                        'return_height' => $each['return_height']
                    ];

                } else if ($each['type'] == 'eenp') {
                    $tfuel = round($each['amount'] / Constants::THETA_WEI, 2);
                    $stakes[] = [
                        'id' => $each['_id'],
                        'role' => $role,
                        'type' => 'eenp',
                        'holder' => $each['holder'],
                        'source' => $each['source'],
                        'coins' => $tfuel,
                        'currency' => 'tfuel',
                        'status' => $each['withdrawn'] ? 'unstaking' : 'staking',
                        'return_height' => $each['return_height']
                    ];

                } else if ($each['type'] == 'vcp') {
                    $theta = round($each['amount'] / Constants::THETA_WEI, 2);
                    $stakes[] = [
                        'id' => $each['_id'],
                        'role' => $role,
                        'type' => 'vcp',
                        'holder' => $each['holder'],
                        'source' => $each['source'],
                        'coins' => $theta,
                        'currency' => 'theta',
                        'status' => $each['withdrawn'] ? 'unstaking' : 'staking',
                        'return_height' => $each['return_height']
                    ];
                }
            }
            return $stakes;

        } else {
            Log::channel('db')->error('Request failed: theta/api/stake');
        }

        return false;
    }


    public function getTransactionDetails($id)
    {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/transaction/' . $id);
        if ($response->ok()) {
            $data = $response->json()['body'];
            $transaction = [
                'id' => $id,
                'block_height' => $data['block_height'],
                'timestamp' => $data['timestamp'],
                'status' => $data['status']
            ];

            if ($data['type'] == 2) { // transfer
                $transaction['type'] = 'transfer';
                $transaction['from_account'] = $data['data']['inputs'][0]['address'];
                $transaction['to_account'] = $data['data']['outputs'][0]['address'];
                $transaction['fee'] = round($data['data']['fee']['tfuelwei'] / Constants::THETA_WEI, 3);

                $theta = round($data['data']['outputs'][0]['coins']['thetawei'] / Constants::THETA_WEI, 3);
                $tfuel = round($data['data']['outputs'][0]['coins']['tfuelwei'] / Constants::THETA_WEI, 3);
                if ($theta > 0) {
                    $transaction['coins'] = $theta;
                    $transaction['currency'] = 'theta';
                } else {
                    $transaction['coins'] = $tfuel;
                    $transaction['currency'] = 'tfuel';
                }

            } else if ($data['type'] == 8 || $data['type'] == 10) { // stake
                $transaction['type'] = 'stake';
                $transaction['from_account'] = $data['data']['source']['address'];
                $transaction['to_account'] = $data['data']['holder']['address'];
                $transaction['fee'] = round($data['data']['fee']['tfuelwei'] / Constants::THETA_WEI, 3);

                $theta = round($data['data']['source']['coins']['thetawei'] / Constants::THETA_WEI, 3);
                $tfuel = round($data['data']['source']['coins']['tfuelwei'] / Constants::THETA_WEI, 3);
                if ($theta > 0) {
                    $transaction['coins'] = $theta;
                    $transaction['currency'] = 'theta';
                } else {
                    $transaction['coins'] = $tfuel;
                    $transaction['currency'] = 'tfuel';
                }

            } else if ($data['type'] == 9) { // withdraw
                $transaction['type'] = 'unstake';
                $transaction['from_account'] = $data['data']['holder']['address'];
                $transaction['to_account'] = $data['data']['source']['address'];
                $transaction['fee'] = round($data['data']['fee']['tfuelwei'] / Constants::THETA_WEI, 3);
                $transaction['coins'] = 0;
                $transaction['currency'] = 'theta';
            }

            return $transaction;

        } else {
            Log::channel('db')->error('Request failed: theta/api/transaction');
        }
        return false;
    }

    public function getStakeBySourceAndHolder($source, $holder)
    {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/stake/' . $source . '?types[]=vcp&types[]=gcp&types[]=eenp');
        if ($response->ok()) {
            $data = $response->json()['body'];
            foreach ($data['sourceRecords'] as $each) {
                if ($each['holder'] == $holder) {
                    return $each;
                }
            }
        } else {
            Log::channel('db')->error('Request failed (getStakeBySourceAndHolder): theta/api/stake');
        }
        return false;
    }

    public function getBlockHeight()
    {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/blocks/top_blocks?pageNumber=1&limit=1');
        if ($response->ok()) {
            return $response->json()['body'][0]['height'];
        } else {
            Log::channel('db')->error('Request failed (getCurrentBlockHeight): theta/api/blocks/top_blocks');
        }
        return false;
    }

    public function getBlocks24h()
    {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/blocks/number/24');
        if ($response->ok()) {
            return $response->json()['body']['total_num_block'];
        } else {
            Log::channel('db')->error('Request failed (getBlocks24H): theta/api/blocks/number/24');
        }
        return false;
    }

    public function getTransactions24h()
    {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/transactions/number/24');
        if ($response->ok()) {
            return $response->json()['body']['total_num_tx'];
        } else {
            Log::channel('db')->error('Request failed (getTransactions24H): theta/api/transactions/number/24');
        }
        return false;
    }

    public function getContractSummary($contractId)
    {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/tokenSummary/' . $contractId);
        if ($response->ok()) {
            return $response->json()['body'];
        } else {
            Log::channel('db')->error('Request failed (getContractSummary): theta/api/tokenSummary');
        }
        return false;
    }

    public function getHistoryPricesInBinance()
    {
        $response = Http::get('https://www.binance.com/api/v3/uiKlines?limit=1000&symbol=TFUELUSDT&interval=1d');
        $data = $response->json();
        $tfuelData = [];
        foreach ($data as $each) {
            $timestamp = substr($each[0], 0, 10);
            $date = date('Y-m-d', $timestamp);
            $price = round($each[1], 5);
            $tfuelData[$date] = $price;
        }

        $response = Http::get('https://www.binance.com/api/v3/uiKlines?limit=1000&symbol=THETAUSDT&interval=1d');
        $data = $response->json();
        $thetaData = [];
        foreach ($data as $each) {
            $timestamp = substr($each[0], 0, 10);
            $date = date('Y-m-d', $timestamp);
            $price = round($each[1], 5);
            $thetaData[$date] = $price;
        }

        $data = [];
        foreach ($tfuelData as $date => $tfuelPrice) {
            $thetaPrice = $thetaData[$date];
            $ratio = round($thetaPrice / $tfuelPrice, 1);
            $data[] = ['date' => $date, 'theta' => $thetaPrice, 'tfuel' => $tfuelPrice, 'ratio' => $ratio];
        }
        return $data;
    }

    public function getTotalTfuelBurnt()
    {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/supply/tfuel/burnt');
        if ($response->ok()) {
            return round($response->json()['total_tfuelwei_burnt'] / Constants::THETA_WEI, 2);
        } else {
            Log::channel('db')->error('Request failed (getTfuelBurnt): theta/api/supply/tfuel/burnt');
        }
        return false;
    }

    public function getDailyTfuelBurnt($timestamp = null)
    {
        $url = Constants::THETA_EXPLORER_API_URL . '/api/supply/dailyTfuelBurnt';
        if ($timestamp) {
            $url .= '?timestamp=' . $timestamp;
        }
        $response = Http::get($url);
        if ($response->ok()) {
            return $response->json()['body'];
        } else {
            Log::channel('db')->error('Request failed (getDailyTfuelBurnt): theta/api/supply/dailyTfuelBurnt');
        }
        return false;
    }

    public function getTotalTfuelSupply()
    {
        return 5000000000 + ~~((10968061 - 4164982) / 100) * 4800 + ~~(($this->getBlockHeight() - 10968061) / 100) * 8600;
    }

    public function getPricesFromBinance() {
        $url = 'https://www.binance.com/api/v3/ticker/price?symbols=[%22BTCUSDT%22,%22THETAUSDT%22,%22TFUELUSDT%22]';
        $response = Http::get($url);
        if ($response->ok()) {
            $prices = [];
            foreach ($response->json() as $each) {
                $prices[str_replace('USDT', '', $each['symbol'])] = $each['price'];
            }
            return $prices;
        } else {
            Log::channel('db')->error('Request failed (getPricesFromBinance): binance/api/v3/ticker/price');
        }
        return false;
    }
}
