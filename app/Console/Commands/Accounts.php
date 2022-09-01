<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Models\Account;
use App\Models\Stake;
use App\Models\TrackingAccount;
use App\Models\Transaction;
use App\Services\OnChainService;
use App\Services\ThetaService;
use App\Services\MessageService;
use Illuminate\Console\Command;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class Accounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(ThetaService $thetaService, OnChainService $onChainService)
    {
        $networkInfo = $thetaService->getNetworkInfo();

        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/account/top/theta/500');
        if (!$response->ok()) {
            Log::channel('db')->error('Request failed: api/account/top/theta');
            return false;
        }
        foreach ($response->json()['body'] as $account) {
            $data = [
                'code' => $account['address'],
                'balance_theta' => round($account['balance']['thetawei'] / Constants::THETA_WEI, 2),
                'balance_tfuel' => round($account['balance']['tfuelwei'] / Constants::THETA_WEI, 2),
            ];
            TrackingAccount::updateOrCreate(
                ['code' => $account['address']],
                $data
            );
        }

        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/account/top/tfuel/500');
        if (!$response->ok()) {
            Log::channel('db')->error('Request failed: api/account/top/tfuel');
            return false;
        }
        foreach ($response->json()['body'] as $account) {
            $data = [
                'code' => $account['address'],
                'balance_theta' => round($account['balance']['thetawei'] / Constants::THETA_WEI, 2),
                'balance_tfuel' => round($account['balance']['tfuelwei'] / Constants::THETA_WEI, 2),
            ];
            TrackingAccount::updateOrCreate(
                ['code' => $account['address']],
                $data
            );
        }

        $trackingAccounts = DB::select('SELECT * FROM tracking_accounts');
        foreach ($trackingAccounts as $trackingAccount) {
            DB::statement("UPDATE tracking_accounts SET balance_usd = ROUND(balance_theta * ? + balance_tfuel * ? + balance_tdrop * ?, 2) WHERE code = ?", [$networkInfo['theta_price'], $networkInfo['tfuel_price'], $networkInfo['tdrop_price'], $trackingAccount->code]);
        }

        DB::statement("DELETE tracking_accounts FROM tracking_accounts LEFT JOIN accounts ON tracking_accounts.code = accounts.code WHERE accounts.id IS NULL AND tracking_accounts.balance_usd <= ?", [Constants::WHALE_MIN_BALANCE]);
        $thetaService->cacheTrackingAccounts();

        $thetaService->setCommandTracker('Accounts', 'last_run', time());
        $this->info('Done');
        return 0;
    }
}
