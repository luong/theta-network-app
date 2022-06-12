<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Services\ThetaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Stake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:stake';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private ThetaService $thetaService;
    private $coinList = null;

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
    public function handle(ThetaService $thetaService)
    {
        $this->info(date('Y-m-d H:i:s'));

        $this->thetaService = $thetaService;
        $this->coinList = $thetaService->getCoinList();
        $this->persistStakes('tfuel', Constants::THETA_EXPLORER_API_URL . '/api/stake/all?types[]=eenp');
        $this->persistStakes('theta', Constants::THETA_EXPLORER_API_URL . '/api/stake/all?types[]=gcp&?types[]=vcp');

        $this->info(date('Y-m-d H:i:s'));

        $this->info('Done');

        return 0;
    }

    private function persistStakes($currency, $url) {
        $response = Http::get($url);

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
                $data[] = [
                    'code' => $stake['_id'],
                    'holder' => $stake['holder'],
                    'source' => $stake['source'],
                    'type' => $stake['type'],
                    'coins' => $coins,
                    'currency' => $currency,
                    'usd' => $usd,
                    'return_height' => $stake['return_height'],
                    'withdrawn' => $stake['withdrawn'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                if (count($data) == 100 || $i == $total - 1) {
                    DB::table('stakes')->insert($data);
                    $data = [];
                }
            }

        }
    }
}
