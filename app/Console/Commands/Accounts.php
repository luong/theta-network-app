<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Models\Account;
use App\Models\Stake;
use App\Models\TrackingAccount;
use App\Models\Transaction;
use App\Services\OnChainService;
use App\Services\TdropContract;
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

        $trackingAccounts = TrackingAccount::all();
        foreach ($trackingAccounts as $trackingAccount) {
            $tdropContract = resolve(TdropContract::class);
            $tdropBalance = $tdropContract->getBalance($trackingAccount->code);
            if ($tdropBalance !== false) {
                $trackingAccount->balance_tdrop = round($tdropBalance, 2);
                $trackingAccount->balance_usd = round($trackingAccount->balance_theta * $networkInfo['theta_price'] + $trackingAccount->balance_tfuel * $networkInfo['tfuel_price'] + $trackingAccount->balance_tdrop * $networkInfo['tdrop_price'], 2);
                $trackingAccount->save();
            }
        }

        DB::statement("DELETE tracking_accounts FROM tracking_accounts LEFT JOIN accounts ON tracking_accounts.code = accounts.code WHERE accounts.id IS NULL AND tracking_accounts.balance_usd <= ?", [Constants::WHALE_MIN_BALANCE]);
        $thetaService->cacheTrackingAccounts();

        $thetaService->setCommandTracker('Accounts', 'last_run', time());
        $this->info('Done');
        return 0;
    }
}
