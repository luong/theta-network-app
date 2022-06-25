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
        $trackingAccounts = TrackingAccount::all();
        foreach ($trackingAccounts as $trackingAccount) {
            $thetaService->updateTrackingAccount($trackingAccount, $networkInfo);
            sleep(2);
        }
        TrackingAccount::whereDate('balance_usd', '<', Constants::WHALE_MIN_BALANCE)->delete();
        $thetaService->cacheTrackingAccounts();
        $this->info('Done');
        return 0;
    }
}
