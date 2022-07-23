<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Models\DailyChain;
use App\Models\DailyCoin;
use App\Services\OnChainService;
use App\Services\ThetaService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DailyStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:dailyStats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update daily stats';

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
    public function handle(OnChainService $onChainService, ThetaService $thetaService)
    {
        if (DailyChain::where('date', Carbon::today())->exists()) {
            $this->info('Existed');
            return 0;
        }

        $stats = $onChainService->getThetaStats();
        $coinList = $thetaService->getCoinList();

        if ($stats !== false) {
            DailyCoin::updateOrCreate(
                ['date' => Carbon::today(), 'coin' => 'theta'],
                ['price' => $stats['theta']['price'], 'market_cap' => $stats['theta']['market_cap'], 'volume_24h' => $stats['theta']['volume_24h'],  'supply' => $stats['theta']['supply'], 'total_stakes' => $stats['theta']['total_stakes'], 'staked_nodes' => $stats['theta']['staked_nodes']]
            );

            $tbillAccount = $onChainService->getAccount(Constants::TBILL_ACCOUNT);
            DailyCoin::updateOrCreate(
                ['date' => Carbon::today(), 'coin' => 'tfuel'],
                ['price' => $stats['tfuel']['price'], 'market_cap' => $stats['tfuel']['market_cap'], 'volume_24h' => $stats['tfuel']['volume_24h'], 'supply' => $stats['tfuel']['supply'], 'total_stakes' => $stats['tfuel']['total_stakes'], 'staked_nodes' => $stats['tfuel']['staked_nodes'], 'locked_supply' => ['tbill' => @$tbillAccount['balance']['tfuel']]]
            );

            DailyCoin::updateOrCreate(
                ['date' => Carbon::today(), 'coin' => 'tdrop'],
                ['price' => $coinList['TDROP']['price'], 'market_cap' => $coinList['TDROP']['market_cap'], 'volume_24h' => $coinList['TDROP']['volume_24h'], 'supply' => $coinList['TDROP']['circulating_supply'], 'total_stakes' => null, 'staked_nodes' => null]
            );

            $nodeStats = $thetaService->getNodeStats();
            $dropStats = $thetaService->getDropStats24H();

            $chain = new DailyChain([
                'date' => Carbon::today(),
                'validators' => $nodeStats['validators'],
                'onchain_wallets' => $stats['network']['onchain_wallets'],
                'active_wallets' => $stats['network']['active_wallets'],
            ]);
            $chain->save();
            $chain->nodes = ['elites' => $nodeStats['elites'], 'guardians' => $nodeStats['guardians']];
            $chain->drops = $dropStats;
            $chain->save();

            $thetaService->cacheNetworkInfo();
            $thetaService->cacheTfuelSupplyChartData();
            $thetaService->cacheTfuelStakeChartData();
            $thetaService->cacheThetaStakeChartData();
            $thetaService->cacheTfuelFreeSupplyChartData();
            $thetaService->cacheEliteNodeChartData();
            $thetaService->cacheThetaDropSalesChartData();

            $thetaService->setCommandTracker('DailyStats', 'last_run', time());
        }

        // Reset settings
        Cache::put('nft_tweet_times', 0);

        $this->info('Done');
        return 0;
    }
}
