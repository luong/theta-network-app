<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Services\ThetaService;
use App\Services\MessageService;
use Illuminate\Console\Command;

class DailyTweet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:dailyTweet';

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

        $tvl = Helper::formatPrice($networkInfo['tvl_value'], 2, 'M') . ' (' . ($networkInfo['tvl_change_24h'] > 0 ? '+' : '') . number_format($networkInfo['tvl_change_24h'] * 100, 2) . '%)';
        $thetaPrice = Helper::formatPrice($coins['THETA']['price']) . ' (' . ($coins['THETA']['price_change_24h'] > 0 ? '+' : '') . round($coins['THETA']['price_change_24h'], 2) . '%) #' . $coins['THETA']['market_cap_rank'];
        $tfuelPrice = Helper::formatPrice($coins['TFUEL']['price']) . ' (' . ($coins['TFUEL']['price_change_24h'] > 0 ? '+' : '') . round($coins['TFUEL']['price_change_24h'], 2) . '%) #' . $coins['TFUEL']['market_cap_rank'];
        $tdropPrice = Helper::formatPrice($coins['TDROP']['price']) . ' (' . ($coins['TDROP']['price_change_24h'] > 0 ? '+' : '') . round($coins['TDROP']['price_change_24h'], 2) . '%) #' . $coins['TDROP']['market_cap_rank'];
        $ratio = round($coins['THETA']['price'] / $coins['TFUEL']['price'], 1);
        $thetaStakes = number_format($networkInfo['theta_stake_rate'] * 100, 2) . '% (' . (($networkInfo['theta_stake_change_24h'] > 0 ? '+' : '') . Helper::formatNumber($networkInfo['theta_stake_change_24h'], 2, 'M')) . ')';
        $tfuelStakes = number_format($networkInfo['tfuel_stake_rate'] * 100, 2) . '% (' . (($networkInfo['tfuel_stake_change_24h'] > 0 ? '+' : '') . Helper::formatNumber($networkInfo['tfuel_stake_change_24h'], 2, 'M')) . ')';
        $tfuelSupply = Helper::formatNumber($networkInfo['tfuel_supply'], 3, 'B') . ' (' . ($networkInfo['tfuel_supply_change_24h'] >= 0 ? '+' : '-') . Helper::formatNumber($networkInfo['tfuel_supply_change_24h'], 2, 'M') . ')';
        $messageService->sendDailyUpdates(compact(['tvl', 'thetaPrice', 'tfuelPrice', 'tdropPrice', 'ratio', 'thetaStakes', 'tfuelStakes', 'tfuelSupply']));

        $thetaService->setCommandTracker('DailyTweet', 'last_run', time());

        $this->info('Done');
        return 0;
    }
}
