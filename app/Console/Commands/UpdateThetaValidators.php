<?php

namespace App\Console\Commands;

use App\Models\DailyChain;
use App\Services\CoinService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateThetaValidators extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:updateThetaValidators';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update theta validators';

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
    public function handle(CoinService $coinService)
    {
        $marketingData = $coinService->getThetaMarketingData();

        $dailyChain = DailyChain::where('date', Carbon::today())->where('chain', 'theta')->first();
        if (empty($dailyChain)) {
            $this->info('Not ready');
            return 0;
        }

        if (empty($dailyChain->validators)) {
            $dailyChain->validators = $marketingData['validators'];
            $dailyChain->metadata = ['edge_nodes' => $marketingData['edge_nodes'], 'guardian_nodes' => $marketingData['guardian_nodes']];
            $dailyChain->save();
        }

        $this->info('Done');
        return 0;
    }
}
