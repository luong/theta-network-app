<?php
namespace App\Console\Commands;

ini_set('memory_limit', -1);

use App\Helpers\Constants;
use App\Mail\WalletRadarEmail;
use App\Models\Account;
use App\Models\Stake;
use App\Models\TrackingAccount;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\OnChainService;
use App\Services\TdropContract;
use App\Services\ThetaService;
use App\Services\MessageService;
use Illuminate\Console\Command;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use TwitterAPIExchange;
use TWOamigos\InputDataDecoder;
use Web3\Contracts\Ethabi;
use Web3\Contracts\Types\Address;
use Web3\Contracts\Types\Boolean;
use Web3\Contracts\Types\Bytes;
use Web3\Contracts\Types\DynamicBytes;
use Web3\Contracts\Types\Integer;
use Web3\Contracts\Types\Str;
use Web3\Contracts\Types\Uinteger;
use Web3\Utils;
use Web3\Web3;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:test';

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
    public function handle(ThetaService $thetaService, OnChainService $onChainService)
    {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/tokenHolder/' . Constants::TDROP_STAKING_ADDRESS);
        if (!$response->ok()) {
            Log::channel('db')->error('Request failed: marketing/v1/news');
            return false;
        }
        $networkInfo = $thetaService->getNetworkInfo();
        $holders = $response->json()['body']['holders'];
        foreach ($holders as $holder) {
            $amount = $holder['amount'] / Constants::THETA_WEI;
            $usd = $amount * $networkInfo['tdrop_price'];
            if ($usd < 500000) {
                continue;
            }
            echo "{$holder['address']} - {$amount} \n";
        }
        return 0;
    }
}
