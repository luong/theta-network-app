<?php

namespace App\Console\Commands;

use App\Services\OnChainService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
    public function handle(OnChainService $coinService)
    {
        $coins = $coinService->getCoinList();
        if ($coins !== false) {
            Cache::put('coins', $coins);
        }
        $this->info('Done');
        return 0;
    }
}
