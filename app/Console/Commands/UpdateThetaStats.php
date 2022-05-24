<?php

namespace App\Console\Commands;

use App\Models\DailyChain;
use App\Models\DailyCoin;
use App\Services\CoinService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateThetaStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:updateThetaStats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update theta stats';

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
    public function handle(CoinService $coinService)
    {
        $stats = $coinService->getThetaStats();

        DailyCoin::updateOrCreate(
            ['date' => Carbon::today(), 'coin' => 'theta'],
            ['price' => $stats['theta']['price'], 'market_cap' => $stats['theta']['market_cap'], 'volume_24h' => $stats['theta']['volume_24h'],  'supply' => $stats['theta']['supply'], 'total_stakes' => $stats['theta']['total_stakes'], 'staked_nodes' => $stats['theta']['staked_nodes']]
        );

        DailyCoin::updateOrCreate(
            ['date' => Carbon::today(), 'coin' => 'tfuel'],
            ['price' => $stats['tfuel']['price'], 'market_cap' => $stats['tfuel']['market_cap'], 'volume_24h' => $stats['tfuel']['volume_24h'], 'supply' => $stats['tfuel']['supply'], 'total_stakes' => $stats['tfuel']['total_stakes'], 'staked_nodes' => $stats['tfuel']['staked_nodes']]
        );

        DailyChain::updateOrCreate(
            ['date' => Carbon::today(), 'chain' => 'theta'],
            ['onchain_wallets' => $stats['network']['onchain_wallets'], 'active_wallets' => $stats['network']['active_wallets']]
        );

        $this->info('Done');
        return 0;
    }
}
