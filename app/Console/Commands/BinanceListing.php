<?php
namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\DexService;
use App\Services\ThetaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BinanceListing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:binanceListing';

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
    public function handle(DexService $dexService, ThetaService $thetaService)
    {
        $newListing = $dexService->getBinanceNewListing();
        $settings = $thetaService->getSettings();

        if ($newListing === false) {
            Log::channel('db')->error('Request failed: Binance listing');
            return 0;
        }

        $thetaService->setCommandTracker('BinanceListing', 'last_run', time());

        if (empty($newListing['coin'])) {
            return 0;
        }
        if ($newListing['coin'] == $settings['binance_new_coin']) {
            return 0;
        }

        $settingObj = Setting::where('code', 'binance_new_coin')->first();
        $settingObj->value = $newListing['coin'];
        $settingObj->save();
        $thetaService->cacheSettings();

        $response = Http::withBody(json_encode(['currency' => $newListing['coin'], 'balanceRate' => 0.95]), 'application/json')
            ->post('https://long10.thetapizza.com/trade');
        if (!$response->ok()) {
            Log::channel('db')->error('Gate orders failed');
            return false;
        }
        Log::channel('db')->error('Gate orders placed: ' . print_r($response->json(), 1));

        return 0;
    }
}
