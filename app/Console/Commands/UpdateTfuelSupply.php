<?php

namespace App\Console\Commands;

use App\Models\TfuelSupply;
use App\Services\CoinService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateTfuelSupply extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:updateTfuel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update tfuel supply';

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
        $tfuelSupply = $coinService->getTfuelSupply();
        if ($tfuelSupply !== false) {
            TfuelSupply::updateOrCreate(
                ['date' => Carbon::today()],
                ['supply' => $tfuelSupply]
            );
        }
        $this->info('Done');
        return 0;
    }
}
