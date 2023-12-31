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

class TdropTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:tdropTransactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update tdrop transactions';

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
        $thetaService = resolve(ThetaService::class);
        $onChainService = resolve(OnChainService::class);
        $messageService = resolve(MessageService::class);
        $accounts = resolve(ThetaService::class)->getAccounts();

        $isVIP = function ($address) use ($accounts) {
            if (isset($accounts[$address]) && (str_contains($accounts[$address]['name'], 'ThetaLabs') || !empty($accounts[$address]['tags']) && in_array('validator_member', $accounts[$address]['tags']))) {
                return true;
            }
            return false;
        };

        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/token/' . Constants::TDROP_CONTRACT_ID . '/?pageNumber=1&limit=' . Constants::TOP_TRANSACTION_LIMIT);
        if (!$response->ok()) {
            Log::channel('db')->error('Request failed: theta/api/token/tdrop');
            return false;
        }

        $coinList = $onChainService->getCoinList();
        $oldTransactionIds = Cache::get('old_tdrop_transaction_ids', []);
        $newTransactionIds = [];

        $trackedData = [];
        $transactions = $response->json()['body'];

        foreach ($transactions as $transaction) {
            if (in_array($transaction['hash'], $oldTransactionIds)) {
                $newTransactionIds[] = $transaction['hash'];
                continue;
            }

            $type = 'transfer';
            if (strtolower($transaction['to']) == strtolower(Constants::TDROP_STAKING_ADDRESS)) {
                $type = 'stake';
            } else if (strtolower($transaction['from']) == strtolower(Constants::TDROP_STAKING_ADDRESS)) {
                $type = 'unstake';
            }

            $tdrop = round($transaction['value'] / Constants::THETA_WEI);
            $usd = round($tdrop * $coinList['TDROP']['price'], 2);

            $tx = [
                'id' => $transaction['hash'],
                'type' => $type,
                'date' => date('Y-m-d H:i', $transaction['timestamp']),
                'from' => $transaction['from'],
                'to' => $transaction['to'],
                'amount' => number_format($tdrop) . ' $tdrop (' . Helper::formatPrice($usd, 0) . ')',
                'coins' => $tdrop,
                'currency' => 'tdrop',
                'usd' => $usd
            ];

            if ($usd >= Constants::TOP_TDROP_TRANSACTION_TWEET_AMOUNT || (($isVIP($tx['from']) || $isVIP($tx['to'])) &&  $usd >= Constants::VIP_TRANSACTION_AMOUNT)) {
                $messageService->hasLargeTransaction($tx);
                $thetaService->addTrackingAccount(strtolower($transaction['to']), null, null, true);
            }

            if ($usd > 0) {
                $trackedData[] = $tx;
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

                $messageService->notifyWalletChanges($each);

                $newTransactionIds[] = $data['txn'];
            }
        }

        Cache::put('old_tdrop_transaction_ids', $newTransactionIds);

        $thetaService->setCommandTracker('TdropTransactions', 'last_run', time());
        $this->info('Done');
        return 0;
    }

}
