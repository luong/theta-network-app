<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Helpers\Helper;
use App\Models\Account;
use App\Models\Stake;
use App\Services\MessageService;
use App\Services\ThetaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Stakes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:stakes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stakes';

    private ThetaService $thetaService;
    private $coinList = null;
    private $networkInfo = null;

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
        $this->thetaService = $thetaService;
        $this->coinList = $this->thetaService->getCoinList();
        $this->networkInfo = $this->thetaService->getNetworkInfo();
        $oldValidators = $this->thetaService->cacheValidators();

        $this->persistStakes('tfuel', '/api/stake/all?types[]=eenp');
        $this->persistStakes('theta', '/api/stake/all?types[]=gcp&types[]=vcp');

        $latestValidators = $this->thetaService->cacheValidators();

        // Check validator changes
        if (!empty($oldValidators) && !empty($latestValidators)) {
            foreach ($latestValidators as $holder => $props) {
                if (!isset($oldValidators[$holder])) {
                    $messageService->hasNewValidator($holder, number_format($props['coins']));
                    Account::updateOrCreate(
                        ['code' => $holder],
                        ['name' => 'Validator']
                    );
                    foreach ($props['stakers'] as $staker) {
                        Account::updateOrCreate(
                            ['code' => $staker['source']],
                            ['name' => 'Validator']
                        );
                    }
                    $thetaService->cacheAccounts();
                }
            }
        }

        $thetaService->cacheValidators();
        $thetaService->updateDailyValidators(count($latestValidators));
        $thetaService->cacheNetworkInfo();
        $thetaService->cacheUnstakings();

        $thetaService->setCommandTracker('Stakes', 'last_run', time());

        $this->info('Done');
        return 0;
    }

    private function persistStakes($currency, $url) {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . $url);

        if ($response->ok()) {
            DB::table('stakes')->where('currency', '=', $currency)->delete();

            $stakes = $response->json()['body'];
            $total = count($stakes);
            $data = [];

            foreach ($stakes as $i => $stake) {
                $coins = round($stake['amount'] / Constants::THETA_WEI, 2);
                $usd = 0;
                if ($currency == 'tfuel') {
                    $usd = round($coins * $this->coinList['TFUEL']['price'], 2);
                } else {
                    $usd = round($coins * $this->coinList['THETA']['price'], 2);
                }

                $seconds = round(((int)$stake['return_height'] - $this->networkInfo['block_height']) / $this->networkInfo['blocks_24h'] * 86400, 0);
                $returnDate = null;
                if ($seconds >= 0 && $seconds < 20 * 86400) {
                    $returnDate = date('Y-m-d H:i', strtotime('+' . $seconds . ' seconds'));
                }

                $data[] = [
                    'code' => $stake['_id'],
                    'holder' => $stake['holder'],
                    'source' => $stake['source'],
                    'type' => $stake['type'],
                    'coins' => $coins,
                    'currency' => $currency,
                    'usd' => $usd,
                    'return_height' => $stake['return_height'],
                    'returned_at' => $returnDate,
                    'withdrawn' => $stake['withdrawn'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                if (count($data) == 100 || $i == $total - 1) {
                    DB::table('stakes')->insert($data);
                    $data = [];
                }
            }
        } else {
            Log::channel('db')->error('Request failed: ' . $url);
        }
    }
}
