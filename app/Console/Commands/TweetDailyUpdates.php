<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Services\ThetaService;
use App\Services\TweetService;
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
    public function handle(ThetaService $thetaService, TweetService $tweetService)
    {
        $coins = $thetaService->getCoinList();
        $networkInfo = $thetaService->getNetworkInfo();

        $btcPrice = Helper::formatPrice($coins['BTC']['price']) . ' (' . round($coins['BTC']['price_change_24h'], 2) . '%)';
        $thetaPrice = Helper::formatPrice($coins['THETA']['price']) . ' (' . round($coins['THETA']['price_change_24h'], 2) . '%)';
        $tfuelPrice = Helper::formatPrice($coins['TFUEL']['price']) . ' (' . round($coins['TFUEL']['price_change_24h'], 2) . '%)';
        $tdropPrice = Helper::formatPrice($coins['TDROP']['price']) . ' (' . round($coins['TDROP']['price_change_24h'], 2) . '%)';
        $ratio = round($coins['THETA']['price'] / $coins['TFUEL']['price'], 1);
        $thetaStakes = number_format($networkInfo['theta_stake_rate'] * 100, 2) . '%';
        $tfuelStakes = number_format($networkInfo['tfuel_stake_rate'] * 100, 2) . '%';

        $text = "[Bot] Daily Updates @Theta_Network: \n - BTC: {$btcPrice} \n - THETA: {$thetaPrice} \n - TFUEL: {$tfuelPrice} \n - TDROP: {$tdropPrice} \n - THETA-TFUEL Ratio: {$ratio} \n - THETA-TFUEL Stakes: {$thetaStakes} - {$tfuelStakes} \n";
        $tweetService->tweetText($text);

        $this->info('Done');
        return 0;
    }
}
