<?php

namespace App\Console\Commands;

use App\Services\ThetaService;
use Illuminate\Console\Command;

class UpdatePrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:updatePrices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update prices';

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
        $thetaService->cacheCoinList();
        $thetaService->cacheNetworkInfo();
        $this->info('Done');
        return 0;
    }
}
