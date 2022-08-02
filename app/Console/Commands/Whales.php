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

class Whales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:whales';

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
        $whaleAccountIds = TrackingAccount::all('code')->pluck('code')->toArray();
        $whales = DB::table('transactions')->whereDate('date', '>=', now()->subDays(30))->selectRaw('to_account, SUM(usd) AS usd')->groupBy(['to_account'])->having('usd', '>=', 99000)->get();
        foreach ($whales as $whale) {
            if (in_array($whale->to_account, $whaleAccountIds)) {
                continue;
            }
            $thetaService->addWhaleAccount($whale->to_account);
            sleep(1);
        }
        $thetaService->cacheTrackingAccounts();

        $thetaService->setCommandTracker('Whales', 'last_run', time());
        $this->info('Done');
        return 0;
    }
}
