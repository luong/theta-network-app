<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Helpers\Helper;
use App\Models\Transaction;
use App\Services\MessageService;
use App\Services\OnChainService;
use App\Services\ThetaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            Log::channel('db')->error('Request failed: theta/api/transactions');
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
                        'coins' => $theta,
                        'currency' => 'theta',
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
                        'coins' => $tfuel,
                        'currency' => 'tfuel',
                        'usd' => $usd
                    ];
                    if ($usd >= Constants::TOP_TRANSACTION_MIN_AMOUNT) {
                        $data[$transaction['_id']] = $tx;
                    }
                    if ($usd >= Constants::TOP_TRANSACTION_TWEET_AMOUNT) {
                        $messageService->hasLargeTransaction($tx);
                    }
                }

                if ($usd > 1) {
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
                        'coins' => $theta,
                        'currency' => 'theta',
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
                        'coins' => $tfuel,
                        'currency' => 'tfuel',
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
        Transaction::whereDate('date', '<=', now()->subDays(Constants::TRANSACTION_LIFETIME_DAYS))->delete();

        $newRecentTransactionIds = [];
        $recentTransactionIds = Cache::get('recent_transaction_ids');
        if (empty($recentTransactionIds)) {
            $recentTransactionIds = [];
        }

        $data = [];
        foreach ($trackedData as $each) {
            $newRecentTransactionIds[] = $each['id'];
            if (in_array($each['id'], $recentTransactionIds)) {
                continue;
            }
            $data[] = [
                'txn' => $each['id'],
                'type' => $each['type'],
                'from_account' => $each['from'],
                'to_account' => $each['to'],
                'amount' => $each['coins'],
                'currency' => $each['currency'],
                'usd' => $each['usd'],
                'date' => $each['date']
            ];
        }

        if (!empty($data)) {
            foreach ($data as $each) {
                Transaction::updateOrCreate(
                    ['txn' => $each['txn']],
                    $each
                );
            }
        }

        Cache::put('recent_transaction_ids', $newRecentTransactionIds);
    }
}
