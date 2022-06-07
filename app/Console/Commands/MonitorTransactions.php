<?php

namespace App\Console\Commands;

use App\Services\OnChainService;
use App\Services\ThetaService;
use Illuminate\Console\Command;

class MonitorTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:monitorTransactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor transactions';

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
        $latestTransactions = $onChainService->getLatestTransactions();
        $thetaService->addTopTransactions($latestTransactions);
        $thetaService->setCommandTracker('MonitorTransactions', 'last_run', time());
        $this->info('Done');
        return 0;
    }
}
