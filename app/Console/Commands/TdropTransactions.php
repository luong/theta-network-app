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
            if (in_array($transaction['_id'], $oldTransactionIds)) {
                $newTransactionIds[] = $transaction['_id'];
                continue;
            }

            $tdrop = round($transaction['value'] / Constants::THETA_WEI);
            $usd = round($tdrop * $coinList['TDROP']['price'], 2);

            $tx = [
                'id' => $transaction['_id'],
                'type' => 'transfer',
                'date' => date('Y-m-d H:i', $transaction['timestamp']),
                'from' => $transaction['from'],
                'to' => $transaction['to'],
                'amount' => number_format($tdrop) . ' $tdrop (' . Helper::formatPrice($usd, 0) . ')',
                'coins' => $tdrop,
                'currency' => 'tdrop',
                'usd' => $usd
            ];

            if ($usd > 1) {
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

                $newTransactionIds[] = $data['txn'];
            }
        }

        Cache::put('old_tdrop_transaction_ids', $newTransactionIds);

        $thetaService->setCommandTracker('TdropTransactions', 'last_run', time());
        $this->info('Done');
        return 0;
    }

}
