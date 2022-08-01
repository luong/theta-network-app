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

class Transactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update transactions';

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

        $coinList = $onChainService->getCoinList();
        $oldTransactionIds = Cache::get('old_transaction_ids', []);
        $newTransactionIds = [];

        $trackedData = [];
        $transactions = $response->json()['body'];

        foreach ($transactions as $transaction) {
            if (in_array($transaction['_id'], $oldTransactionIds)) {
                $newTransactionIds[] = $transaction['_id'];
                continue;
            }

            if ($transaction['type'] == 2) { // transfer
                $usd = 0;
                $theta = round($transaction['data']['outputs'][0]['coins']['thetawei'] / Constants::THETA_WEI, 2);
                $tfuel = round($transaction['data']['outputs'][0]['coins']['tfuelwei'] / Constants::THETA_WEI, 2);

                if ($theta > 0) {
                    $usd = round($theta * $coinList['THETA']['price'], 2);
                    $tx = [
                        'id' => $transaction['_id'],
                        'type' => 'transfer',
                        'type_number' => $transaction['type'],
                        'node' => '',
                        'date' => date('Y-m-d H:i', $transaction['timestamp']),
                        'from' => $transaction['data']['inputs'][0]['address'],
                        'to' => $transaction['data']['outputs'][0]['address'],
                        'amount' => Helper::formatNumber($theta, 2) . ' $theta (' . Helper::formatPrice($usd, 2) . ')',
                        'coins' => $theta,
                        'currency' => 'theta',
                        'usd' => $usd
                    ];
                    if ($usd >= Constants::TOP_TRANSACTION_TWEET_AMOUNT) {
                        $messageService->hasLargeTransaction($tx);
                        $thetaService->addWhaleAccount($transaction['data']['outputs'][0]['address']);
                    }
                    if ($tx['to'] == Constants::DONATE_ACCOUNT_ID) {
                        $messageService->thankForDonation($tx);
                    }

                } else {
                    $usd = round($tfuel * $coinList['TFUEL']['price'], 2);
                    $tx = [
                        'id' => $transaction['_id'],
                        'type' => 'transfer',
                        'type_number' => $transaction['type'],
                        'node' => '',
                        'date' => date('Y-m-d H:i', $transaction['timestamp']),
                        'from' => $transaction['data']['inputs'][0]['address'],
                        'to' => $transaction['data']['outputs'][0]['address'],
                        'amount' => Helper::formatNumber($tfuel, 2) . ' $tfuel (' . Helper::formatPrice($usd, 2) . ')',
                        'coins' => $tfuel,
                        'currency' => 'tfuel',
                        'usd' => $usd
                    ];
                    if ($usd >= Constants::TOP_TRANSACTION_TWEET_AMOUNT) {
                        $messageService->hasLargeTransaction($tx);
                        $thetaService->addWhaleAccount($transaction['data']['outputs'][0]['address']);
                    }
                    if ($tx['to'] == Constants::DONATE_ACCOUNT_ID) {
                        $messageService->thankForDonation($tx);
                    }
                }

                if ($usd > 1) {
                    $trackedData[] = $tx;
                }

            } else if ($transaction['type'] == 10) { // stake as guardian
                $usd = 0;
                $theta = round($transaction['data']['source']['coins']['thetawei'] / Constants::THETA_WEI, 2);
                $tfuel = round($transaction['data']['source']['coins']['tfuelwei'] / Constants::THETA_WEI, 2);
                if ($theta > 0) {
                    $usd = round($theta * $coinList['THETA']['price'], 2);
                    $tx = [
                        'id' => $transaction['_id'],
                        'type' => 'stake',
                        'type_number' => $transaction['type'],
                        'node' => 'guardian',
                        'date' => date('Y-m-d H:i', $transaction['timestamp']),
                        'from' => $transaction['data']['source']['address'],
                        'to' => $transaction['data']['holder']['address'],
                        'amount' => Helper::formatNumber($theta, 2) . ' $theta (' . Helper::formatPrice($usd, 2) . ')',
                        'coins' => $theta,
                        'currency' => 'theta',
                        'usd' => $usd
                    ];
                    if ($usd >= Constants::TOP_TRANSACTION_TWEET_AMOUNT) {
                        $messageService->hasLargeTransaction($tx);
                    }

                } else {
                    $usd = round($tfuel * $coinList['TFUEL']['price'], 2);
                    $tx = [
                        'id' => $transaction['_id'],
                        'type' => 'stake',
                        'type_number' => $transaction['type'],
                        'node' => 'elite',
                        'date' => date('Y-m-d H:i', $transaction['timestamp']),
                        'from' => $transaction['data']['source']['address'],
                        'to' => $transaction['data']['holder']['address'],
                        'amount' => Helper::formatNumber($tfuel, 2) . ' $tfuel (' . Helper::formatPrice($usd, 2) . ')',
                        'coins' => $tfuel,
                        'currency' => 'tfuel',
                        'usd' => $usd
                    ];
                    if ($usd >= Constants::TOP_TRANSACTION_TWEET_AMOUNT) {
                        $messageService->hasLargeTransaction($tx);
                    }
                }

                $trackedData[] = $tx;

            } else if ($transaction['type'] == 8) { // stake as validator
                $theta = round($transaction['data']['source']['coins']['thetawei'] / Constants::THETA_WEI, 2);
                $usd = round($theta * $coinList['THETA']['price'], 2);
                $tx = [
                    'id' => $transaction['_id'],
                    'type' => 'stake',
                    'type_number' => $transaction['type'],
                    'node' => 'validator',
                    'date' => date('Y-m-d H:i', $transaction['timestamp']),
                    'from' => $transaction['data']['source']['address'],
                    'to' => $transaction['data']['holder']['address'],
                    'amount' => Helper::formatNumber($theta, 2) . ' $theta (' . Helper::formatPrice($usd, 2) . ')',
                    'coins' => $theta,
                    'currency' => 'theta',
                    'usd' => $usd
                ];
                if ($usd >= Constants::TOP_TRANSACTION_TWEET_AMOUNT) {
                    $messageService->hasLargeTransaction($tx);
                }
                $trackedData[] = $tx;

            } else if ($transaction['type'] == 9) { // withdraw
                $stakeInfo = $onChainService->getStakeBySourceAndHolder($transaction['data']['source']['address'], $transaction['data']['holder']['address']);
                if (!empty($stakeInfo)) {
                    if ($transaction['data']['purpose'] == 1) { // guardian
                        $theta = round($stakeInfo['amount'] / Constants::THETA_WEI, 2);
                        $usd = round($theta * $coinList['THETA']['price'], 2);
                        $tx = [
                            'id' => $transaction['_id'],
                            'type' => 'unstake',
                            'type_number' => $transaction['type'],
                            'node' => '',
                            'date' => date('Y-m-d H:i', $transaction['timestamp']),
                            'from' => $transaction['data']['holder']['address'],
                            'to' => $transaction['data']['source']['address'],
                            'amount' => Helper::formatNumber($theta, 2) . ' $theta (' . Helper::formatPrice($usd, 2) . ')',
                            'coins' => $theta,
                            'currency' => 'theta',
                            'usd' => $usd
                        ];
                        if ($usd >= Constants::TOP_TRANSACTION_TWEET_AMOUNT) {
                            $messageService->hasLargeTransaction($tx);
                        }
                        $trackedData[] = $tx;

                    } else if ($transaction['data']['purpose'] == 2) { // elite
                        $tfuel = round($stakeInfo['amount'] / Constants::THETA_WEI, 2);
                        $usd = round($tfuel * $coinList['TFUEL']['price'], 2);
                        $tx = [
                            'id' => $transaction['_id'],
                            'type' => 'unstake',
                            'type_number' => $transaction['type'],
                            'node' => '',
                            'date' => date('Y-m-d H:i', $transaction['timestamp']),
                            'from' => $transaction['data']['holder']['address'],
                            'to' => $transaction['data']['source']['address'],
                            'amount' => Helper::formatNumber($tfuel, 2) . ' $tfuel (' . Helper::formatPrice($usd, 2) . ')',
                            'coins' => $tfuel,
                            'currency' => 'tfuel',
                            'usd' => $usd
                        ];
                        if ($usd >= Constants::TOP_TRANSACTION_TWEET_AMOUNT) {
                            $messageService->hasLargeTransaction($tx);
                        }
                        $trackedData[] = $tx;
                    }
                }
            }
        }

        if (!empty($trackedData)) {
            Transaction::whereDate('date', '<=', now()->subDays(Constants::TRANSACTION_LIFETIME_DAYS))->delete();
            foreach ($trackedData as $each) {
                $data = [
                    'txn' => $each['id'],
                    'type' => $each['type'],
                    'from_account' => $each['from'],
                    'to_account' => $each['to'],
                    'coins' => $each['coins'],
                    'currency' => $each['currency'],
                    'usd' => $each['usd'],
                    'date' => $each['date']
                ];

                Transaction::updateOrCreate(
                    ['txn' => $data['txn']],
                    $data
                );

                $newTransactionIds[] = $data['txn'];
            }

            $thetaService->cacheTopTransactions();
        }

        Cache::put('old_transaction_ids', $newTransactionIds);

        $thetaService->setCommandTracker('Transactions', 'last_run', time());
        $this->info('Done');
        return 0;
    }

}
