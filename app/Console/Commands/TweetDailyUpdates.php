<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Services\ThetaService;
use App\Services\MessageService;
use Illuminate\Console\Command;

class TweetDailyUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:tweetDailyUpdates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tweet daily updates';

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
    public function handle(ThetaService $thetaService, MessageService $messageService)
    {
        $coins = $thetaService->getCoinList();
        $networkInfo = $thetaService->getNetworkInfo();

        $tvl = Helper::formatPrice($networkInfo['tvl_value'], 2, 'M') . ' (' . number_format($networkInfo['tvl_change_24h'] * 100, 2) . '%)';
        $thetaPrice = Helper::formatPrice($coins['THETA']['price']) . ' (' . round($coins['THETA']['price_change_24h'], 2) . '%) #' . $coins['THETA']['market_cap_rank'];
        $tfuelPrice = Helper::formatPrice($coins['TFUEL']['price']) . ' (' . round($coins['TFUEL']['price_change_24h'], 2) . '%) #' . $coins['TFUEL']['market_cap_rank'];
        $tdropPrice = Helper::formatPrice($coins['TDROP']['price']) . ' (' . round($coins['TDROP']['price_change_24h'], 2) . '%) #' . $coins['TDROP']['market_cap_rank'];
        $ratio = round($coins['THETA']['price'] / $coins['TFUEL']['price'], 1);
        $thetaStakes = number_format($networkInfo['theta_stake_rate'] * 100, 2) . '%';
        $tfuelStakes = number_format($networkInfo['tfuel_stake_rate'] * 100, 2) . '%';
        $tfuelSupply = number_format($networkInfo['tfuel_supply']) . ' (' . ($networkInfo['tfuel_supply_change_24h'] >= 0 ? '+' : '-') . $networkInfo['tfuel_supply_change_24h'] . ')';
        $messageService->sendDailyUpdates(compact(['tvl', 'thetaPrice', 'tfuelPrice', 'tdropPrice', 'ratio', 'thetaStakes', 'tfuelStakes', 'tfuelSupply']));

        $this->info('Done');
        return 0;
    }
}
