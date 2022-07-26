<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Services\ThetaService;
use App\Services\MessageService;
use Illuminate\Console\Command;

class DailyTweet2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:dailyTweet2';

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

        $now = date('Y-m-d H:i') . ' UTC';
        $tvl = Helper::formatPrice($networkInfo['tvl_value'], 2, 'M') . ' (' . ($networkInfo['tvl_change_24h'] > 0 ? '+' : '') . number_format($networkInfo['tvl_change_24h'] * 100, 2) . '%)';
        $validators = $networkInfo['validators'];
        $guardians = number_format($networkInfo['guardian_nodes']) . ' (' . ($networkInfo['guardian_nodes_change_24h'] > 0 ? '+' : '') . number_format($networkInfo['guardian_nodes_change_24h']) .  ')';
        $elites = number_format($networkInfo['elite_nodes']) . ' (' . ($networkInfo['elite_nodes_change_24h'] > 0 ? '+' : '') . number_format($networkInfo['elite_nodes_change_24h']) .  ')';
        $activeWallets = number_format($networkInfo['active_wallets']) . ' (' . ($networkInfo['active_wallets_change_24h'] > 0 ? '+' : '') . number_format($networkInfo['active_wallets_change_24h']) .  ')';

        $btcPrice = Helper::formatPrice($coins['BTC']['price']) . ' (' . ($coins['BTC']['price_change_24h'] > 0 ? '+' : '') . round($coins['BTC']['price_change_24h'], 2) . '%)';
        $thetaPrice = Helper::formatPrice($coins['THETA']['price']) . ' (' . ($coins['THETA']['price_change_24h'] > 0 ? '+' : '') . round($coins['THETA']['price_change_24h'], 2) . '%)';
        $tfuelPrice = Helper::formatPrice($coins['TFUEL']['price']) . ' (' . ($coins['TFUEL']['price_change_24h'] > 0 ? '+' : '') . round($coins['TFUEL']['price_change_24h'], 2) . '%)';
        $tdropPrice = Helper::formatPrice($coins['TDROP']['price']) . ' (' . ($coins['TDROP']['price_change_24h'] > 0 ? '+' : '') . round($coins['TDROP']['price_change_24h'], 2) . '%)';
        $ratio = round($coins['THETA']['price'] / $coins['TFUEL']['price'], 1);
        $thetaStakes = number_format($networkInfo['theta_stake_rate'] * 100, 2) . '% (' . (($networkInfo['theta_stake_change_24h'] > 0 ? '+' : '') . Helper::formatNumber($networkInfo['theta_stake_change_24h'], 2, 'M')) . ')';
        $tfuelStakes = number_format($networkInfo['tfuel_stake_rate'] * 100, 2) . '% (' . (($networkInfo['tfuel_stake_change_24h'] > 0 ? '+' : '') . Helper::formatNumber($networkInfo['tfuel_stake_change_24h'], 2, 'M')) . ')';
        $tfuelSupply = Helper::formatNumber($networkInfo['tfuel_supply'], 3, 'B') . ' (' . ($networkInfo['tfuel_supply_change_24h'] >= 0 ? '+' : '-') . Helper::formatNumber($networkInfo['tfuel_supply_change_24h'], 2, 'M') . ')';
        $thetaVol24h = Helper::formatPrice($coins['THETA']['volume_24h'], 2, 'M') . ' (' . ($networkInfo['theta_volume_change_24h'] >= 0 ? '+' : '-') . Helper::formatNumber($networkInfo['theta_volume_change_24h'], 2, 'M') . ')';
        $tfuelVol24h = Helper::formatPrice($coins['TFUEL']['volume_24h'], 2, 'M') . ' (' . ($networkInfo['tfuel_volume_change_24h'] >= 0 ? '+' : '-') . Helper::formatNumber($networkInfo['tfuel_volume_change_24h'], 2, 'M') . ')';
        $tdropVol24h = Helper::formatPrice($coins['TDROP']['volume_24h'], 2, 'M') . ' (' . ($networkInfo['tdrop_volume_change_24h'] >= 0 ? '+' : '-') . Helper::formatNumber($networkInfo['tdrop_volume_change_24h'], 2, 'M') . ')';
        $tdropStakes = number_format($networkInfo['tdrop_stake_rate'] * 100, 2) . '% (' . (($networkInfo['tdrop_stake_change_24h'] > 0 ? '+' : '') . Helper::formatNumber($networkInfo['tdrop_stake_change_24h'], 2, 'M')) . ')';
        $tdropSupply = Helper::formatNumber($networkInfo['tdrop_supply'], 3, 'B') . ' (' . ($networkInfo['tdrop_supply_change_24h'] >= 0 ? '+' : '-') . Helper::formatNumber($networkInfo['tdrop_supply_change_24h'], 2, 'M') . ')';
        $thetaMarketCap = Helper::formatPrice($coins['THETA']['market_cap'], 2, 'B');
        $tfuelMarketCap = Helper::formatPrice($coins['TFUEL']['market_cap'], 2, 'M');
        $tdropMarketCap = Helper::formatPrice($coins['TDROP']['market_cap'], 2, 'M');
        $dropTransactions = Helper::formatNumber($networkInfo['drop_24h']['times']) . ' (' . ($networkInfo['drop_times_change_24h'] >= 0 ? '+' : '-') . Helper::formatNumber($networkInfo['drop_times_change_24h'] * 100, 2) . '%)';
        $dropSales = Helper::formatPrice($networkInfo['drop_24h']['total']) . ' (' . ($networkInfo['drop_sales_change_24h'] >= 0 ? '+' : '-') . Helper::formatNumber($networkInfo['drop_sales_change_24h'] * 100, 2) . '%)';

        $fontLight = public_path('fonts/Roboto-Light.ttf');
        $fontRegular = public_path('fonts/Roboto-Regular.ttf');
        $fontBold = public_path('fonts/Roboto-Bold.ttf');
        $lineHeight = 23;
        $fontHeadingSize = 13;
        $fontSize = 11;

        $xCol1 = 35;
        $xCol2 = 280;
        $yRow0 = 50;
        $yRowBTC = 35;
        $yRow1 = 115;
        $yRow2 = 280;
        $yRow3 = 440;

        $image = imagecreatefrompng(public_path('images/dailybg.png'));
        $textColor = imagecolorallocate($image, 255, 255, 255);

        // Title
        $y = $yRow0;
        imagettftext($image, 18, 0, $xCol1, $y, $textColor, $fontBold, 'THETA Updates');
        $y += $lineHeight;
        imagettftext($image, 10, 0, $xCol1, $y, $textColor, $fontLight, '@ThetaPizza ' . $now);
        imagerectangle($image, $xCol1 - 10, $y - 50, 250, 85, $textColor);

        // Network
        $x = $xCol1;
        $y = $yRow1;
        imagettftext($image, $fontHeadingSize, 0, $x - 10, $y, $textColor, $fontBold, '* Network');
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'TVL: ' . $tvl);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Validators: ' . $validators);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Guardians: ' . $guardians);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Elite nodes: ' . $elites);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Active wallets: ' . $activeWallets);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Theta Tfuel ratio: ' . $ratio);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Blocks 24H: ' . Helper::formatNumber($networkInfo['blocks_24h'], 0));
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Transactions 24H: ' . Helper::formatNumber($networkInfo['transactions_24h'], 0));

        // BTC
        $x = $xCol2;
        $y = $yRowBTC;
        imagettftext($image, $fontHeadingSize, 0, $x - 10, $y, $textColor, $fontBold, '* BTC');
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Price: ' . $btcPrice);

        // Theta
        $x = $xCol2;
        $y = $yRow1 - 25;
        imagettftext($image, $fontHeadingSize, 0, $x - 10, $y, $textColor, $fontBold, '* Theta');
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Price: ' . $thetaPrice);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Vol. 24H: ' . $thetaVol24h);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'M. Cap: ' . $thetaMarketCap);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Ranking: ' . $coins['THETA']['market_cap_rank']);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Staking: ' . $thetaStakes);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Supply: 1B');

        // Tfuel
        $x = $xCol1;
        $y = $yRow2 + 50;
        imagettftext($image, $fontHeadingSize, 0, $x - 10, $y, $textColor, $fontBold, '* Tfuel');
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Price: ' . $tfuelPrice);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Vol. 24H: ' . $tfuelVol24h);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'M. Cap: ' . $tfuelMarketCap);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Ranking: ' . $coins['TFUEL']['market_cap_rank']);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Staking: ' . $tfuelStakes);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Supply: ' . $tfuelSupply);

        // Tdrop
        $x = $xCol2;
        $y = $yRow2 - 20;
        imagettftext($image, $fontHeadingSize, 0, $x - 10, $y, $textColor, $fontBold, '* Tdrop');
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Price: ' . $tdropPrice);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Vol. 24H: ' . $tdropVol24h);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'M. Cap: ' . $tdropMarketCap);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Ranking: ' . Helper::formatNumber($coins['TDROP']['market_cap_rank']));
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Staking: ' . $tdropStakes);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Supply: ' . $tdropSupply);

        // Theta Drop
        $x = $xCol2;
        $y = $yRow3 - 10;
        imagettftext($image, $fontHeadingSize, 0, $x - 10, $y, $textColor, $fontBold, '* Theta Drop');
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Transactions: ' . $dropTransactions);
        $y += $lineHeight;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontRegular, 'Total Sales: ' . $dropSales);

        // Export
        $fileName = 'app/' . uniqid() . '.png';
        $filePath = storage_path($fileName);
        imagepng($image, $filePath, 9, -1);
        imagedestroy($image);

        if (file_exists($filePath)) {
            $messageService->sendDailyUpdatesV2($filePath);
            unlink($filePath);
        }

        $thetaService->setCommandTracker('DailyTweet2', 'last_run', time());

        $this->info('Done');
        return 0;
    }
}
