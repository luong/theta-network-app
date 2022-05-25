<?php

namespace App\Console\Commands;

use App\Services\OnChainService;
use App\Services\ThetaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MonitorThetaTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:monitorThetaTransactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor theta transactions';

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
    public function handle(OnChainService $onChainService, ThetaService $thetaService)
    {
        $limit = 100;
        $latestTransactions = $onChainService->getLatestTransactions();

        $topTransactions = $thetaService->getTopTransactions();
        if (!empty($latestTransactions)) {
            $topTransactions = $latestTransactions + $topTransactions;
            uasort($topTransactions, function($tx1, $tx2) {
                if ($tx1['date'] < $tx2['date']) {
                    return 1;
                } else {
                    return -1;
                }
            });
        }
        if (count($topTransactions) > $limit) {
            $topTransactions = array_slice($topTransactions, 0, $limit);
        }
        Cache::put('top_transactions', $topTransactions);

        return 0;
    }
}
