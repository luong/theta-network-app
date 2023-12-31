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
        $networkInfo = $thetaService->getNetworkInfo();

        if ($stats !== false) {
            DailyCoin::updateOrCreate(
                ['date' => Carbon::today(), 'coin' => 'theta'],
                ['ranking' => $coinList['THETA']['market_cap_rank'], 'price' => $stats['theta']['price'], 'market_cap' => $stats['theta']['market_cap'], 'volume_24h' => $stats['theta']['volume_24h'],  'supply' => $stats['theta']['supply'], 'total_stakes' => $stats['theta']['total_stakes'], 'staked_nodes' => $stats['theta']['staked_nodes']]
            );

            $wtfuelAccount = $onChainService->getAccount(Constants::WTFUEL_ACCOUNT);
            $totalSupply = $onChainService->getTotalTfuelSupply();
            $totalBurnt = $onChainService->getTotalTfuelBurnt();
            DailyCoin::updateOrCreate(
                ['date' => Carbon::today(), 'coin' => 'tfuel'],
                ['ranking' => $coinList['TFUEL']['market_cap_rank'], 'price' => $stats['tfuel']['price'], 'market_cap' => $stats['tfuel']['market_cap'], 'volume_24h' => $stats['tfuel']['volume_24h'], 'supply' => $stats['tfuel']['supply'], 'total_stakes' => $stats['tfuel']['total_stakes'], 'staked_nodes' => $stats['tfuel']['staked_nodes'], 'locked_supply' => ['wtfuel' => @$wtfuelAccount['balance']['tfuel']], 'total_supply' => $totalSupply, 'total_burnt' => $totalBurnt]
            );

            DailyCoin::updateOrCreate(
                ['date' => Carbon::today(), 'coin' => 'tdrop'],
                ['ranking' => $coinList['TDROP']['market_cap_rank'], 'price' => $coinList['TDROP']['price'], 'market_cap' => $coinList['TDROP']['market_cap'], 'volume_24h' => $coinList['TDROP']['volume_24h'], 'supply' => $coinList['TDROP']['circulating_supply'], 'total_stakes' => $stats['tdrop']['total_stakes'], 'staked_nodes' => null]
            );

            $nodeStats = $thetaService->getNodeStats();
            $dropStats = $thetaService->getDropStats24H();

            $chain = new DailyChain([
                'date' => Carbon::today(),
                'validators' => $nodeStats['validators'],
                'onchain_wallets' => $stats['network']['onchain_wallets'],
                'active_wallets' => $stats['network']['active_wallets'],
                'transactions_24h' => $networkInfo['transactions_24h'],
                'blocks_24h' => $networkInfo['blocks_24h']
            ]);
            $chain->save();
            $chain->nodes = ['elites' => $nodeStats['elites'], 'guardians' => $nodeStats['guardians']];
            $chain->drops = $dropStats;
            $chain->save();

            $thetaService->cacheNetworkInfo();
            $thetaService->cacheHistoryPrices();

            $thetaService->cacheChainData();
            $thetaService->cacheThetaData();
            $thetaService->cacheTfuelData();
            $thetaService->cacheTdropData();

            $thetaService->setCommandTracker('DailyStats', 'last_run', time());
        }

        // Reset settings
        Cache::put('nft_tweet_times', 0);

        $this->info('Done');
        return 0;
    }
}
