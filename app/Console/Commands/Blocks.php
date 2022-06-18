<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Helpers\Helper;
use App\Models\DailyCoin;
use App\Models\Transaction;
use App\Services\MessageService;
use App\Services\OnChainService;
use App\Services\ThetaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Blocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:blocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update blocks';

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
    public function handle()
    {
        $thetaService = resolve(ThetaService::class);
        $onChainService = resolve(OnChainService::class);

        $oldBlockIds = Cache::get('old_block_ids', []);
        $newBlockIds = [];

        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/blocks/top_blocks?pageNumber=1&limit=' . Constants::TOP_TRANSACTION_BLOCK);
        if (!$response->ok()) {
            Log::channel('db')->error('Request failed: theta/api/blocks/top_blocks');
            return false;
        }

        $blocks = $response->json()['body'];
        $data = [];
        foreach ($blocks as $block) {
            $newBlockIds[] = $block['height'];
            if (in_array($block['height'], $oldBlockIds)) {
                continue;
            }
            $date = date('Y-m-d', $block['timestamp']);
            if (!isset($data[$date])) {
                $data[$date] = 0;
            }
            $amount = 0;
            foreach ($block['txs'] as $transaction) {
                if (in_array($transaction['type'], [2, 8, 9, 10])) {
                    $amount += 0.3;
                } else if ($transaction['type'] == 7) {
                    $amount += implode('', $transaction['raw']['fee']['tfuelwei']['c']) / Constants::THETA_WEI;
                }
            }
            $data[$date] += $amount;
        }

        if (!empty($data)) {
            foreach ($data as $date => $amount) {
                DB::table('daily_coins')
                    ->where('coin', 'tfuel')
                    ->where('date', $date)
                    ->update(['burned' => DB::raw('burned + ' . round($amount, 5))]);
            }
        }

        Cache::put('old_block_ids', $newBlockIds);
        $thetaService->setCommandTracker('Blocks', 'last_run', time());
        $this->info('Done');
        return 0;
    }

}
