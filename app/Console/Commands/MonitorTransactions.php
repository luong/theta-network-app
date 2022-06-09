<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Helpers\Helper;
use App\Services\MessageService;
use App\Services\OnChainService;
use App\Services\ThetaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MonitorTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:monitorTransactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor transactions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $messageService = resolve(MessageService::class);
        $thetaService = resolve(ThetaService::class);
        $onChainService = resolve(OnChainService::class);

        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/transactions/range?limit=' . Constants::TOP_TRANSACTION_LIMIT);
        if (!$response->ok()) {
            return false;
        }

        $data = [];
        $trackedData = [];
        $transactions = $response->json()['body'];
        $coins = $onChainService->getCoinList();
        $cachedTopTransactions = $thetaService->getTopTransactions();

        foreach ($transactions as $transaction) {
            if (isset($cachedTopTransactions[$transaction['_id']])) {
                continue;
            }

            if ($transaction['type'] == 2) { // transfer
                $usd = 0;
                $theta = round($transaction['data']['outputs'][0]['coins']['thetawei'] / Constants::THETA_WEI);
                $tfuel = round($transaction['data']['outputs'][0]['coins']['tfuelwei'] / Constants::THETA_WEI);

                if ($theta > 0) {
                    $usd = round($theta * $coins['THETA']['price'], 2);
                    $tx = [
                        'id' => $transaction['_id'],
                        'type' => 'transfer',
                        'date' => date('Y-m-d H:i', $transaction['timestamp']),
                        'from' => $transaction['data']['inputs'][0]['address'],
                        'to' => $transaction['data']['outputs'][0]['address'],
                        'amount' => number_format($theta) . ' $theta (' . Helper::formatPrice($usd, 0) . ')',
                        'usd' => $usd
                    ];
                    if ($usd >= Constants::TOP_TRANSACTION_MIN_AMOUNT || $theta >= Constants::THETA_VALIDATOR_MIN_AMOUNT) {
                        $data[$transaction['_id']] = $tx;
                    }
                    if ($usd >= Constants::TOP_TRANSACTION_TWEET_AMOUNT || $theta >= Constants::THETA_VALIDATOR_MIN_AMOUNT) {
                        $messageService->hasLargeTransaction($tx);
                    }

                } else {
                    $usd = round($tfuel * $coins['TFUEL']['price'], 2);
                    $tx = [
                        'id' => $transaction['_id'],
                        'type' => 'transfer',
                        'date' => date('Y-m-d H:i', $transaction['timestamp']),
                        'from' => $transaction['data']['inputs'][0]['address'],
                        'to' => $transaction['data']['outputs'][0]['address'],
                        'amount' => number_format($tfuel) . ' $tfuel (' . Helper::formatPrice($usd, 0) . ')',
                        'usd' => $usd
                    ];
                    if ($usd >= Constants::TOP_TRANSACTION_MIN_AMOUNT) {
                        $data[$transaction['_id']] = $tx;
                    }
                    if ($usd >= Constants::TOP_TRANSACTION_TWEET_AMOUNT) {
                        $messageService->hasLargeTransaction($tx);
                    }
                }

                if ($usd > 5) {
                    $trackedData[] = $tx;
                }

            } else if ($transaction['type'] == 10) { // stake
                $usd = 0;
                $theta = round($transaction['data']['source']['coins']['thetawei'] / Constants::THETA_WEI);
                $tfuel = round($transaction['data']['source']['coins']['tfuelwei'] / Constants::THETA_WEI);
                if ($theta > 0) {
                    $usd = round($theta * $coins['THETA']['price'], 2);
                    $tx = [
                        'id' => $transaction['_id'],
                        'type' => 'stake',
                        'date' => date('Y-m-d H:i', $transaction['timestamp']),
                        'from' => $transaction['data']['source']['address'],
                        'amount' => number_format($theta) . ' $theta (' . Helper::formatPrice($usd, 0) . ')',
                        'usd' => $usd
                    ];
                    if ($usd >= Constants::TOP_TRANSACTION_MIN_AMOUNT || $theta >= Constants::THETA_VALIDATOR_MIN_AMOUNT) {
                        $data[$transaction['_id']] = $tx;
                    }
                    if ($usd >= Constants::TOP_TRANSACTION_TWEET_AMOUNT || $theta >= Constants::THETA_VALIDATOR_MIN_AMOUNT) {
                        $messageService->hasLargeTransaction($tx);
                    }

                } else {
                    $usd = round($tfuel * $coins['TFUEL']['price'], 2);
                    $tx = [
                        'id' => $transaction['_id'],
                        'type' => 'state',
                        'date' => date('Y-m-d H:i', $transaction['timestamp']),
                        'from' => $transaction['data']['source']['address'],
                        'amount' => number_format($tfuel) . ' $tfuel (' . Helper::formatPrice($usd, 0) . ')',
                        'usd' => $usd
                    ];
                    if ($usd >= Constants::TOP_TRANSACTION_MIN_AMOUNT) {
                        $data[$transaction['_id']] = $tx;
                    }
                    if ($usd >= Constants::TOP_TRANSACTION_TWEET_AMOUNT) {
                        $messageService->hasLargeTransaction($tx);
                    }
                }
            }
        }

        if (!empty($data)) {
            $thetaService->addTopTransactions($data);
        }
        if (!empty($trackedData)) {
            $this->trackActivities($trackedData);
        }

        $thetaService->setCommandTracker('MonitorTransactions', 'last_run', time());
        $this->info('Done');
        return 0;
    }

    private function trackActivities($trackedData)
    {
        $newRecentTransactionIds = [];
        $recentTransactionIds = Cache::get('recent_transaction_ids');
        if (empty($recentTransactionIds)) {
            $recentTransactionIds = [];
        }

        $activists = Cache::get('top_activists');
        if (empty($activists)) {
            $activists = [];
        }

        $minDate = date('Y-m-d H:i', strtotime('-24 hours'));
        $newActivists = [];
        foreach ($activists as $key => $count) {
            [$date, $account] = explode('*', $key, 2);
            if ($date >= $minDate) {
                $newActivists[$key] = $count;
            }
        }

        foreach ($trackedData as $each) {
            $newRecentTransactionIds[] = $each['id'];
            if (in_array($each['id'], $recentTransactionIds)) {
                continue;
            }
            $key = $each['date'] . '*' . $each['from'];
            if (!isset($newActivists[$key])) {
                $newActivists[$key] = ['times' => 0, 'usd' => 0];
            }
            $newActivists[$key]['times']++;
            $newActivists[$key]['usd'] += $each['usd'];

            $key = $each['date'] . '*' . $each['to'];
            if (!isset($newActivists[$key])) {
                $newActivists[$key] = ['times' => 0, 'usd' => 0];
            }
            $newActivists[$key]['times']++;
            $newActivists[$key]['usd'] += $each['usd'];
        }

        Cache::put('top_activists', $newActivists);
        Cache::put('recent_transaction_ids', $newRecentTransactionIds);
    }
}
