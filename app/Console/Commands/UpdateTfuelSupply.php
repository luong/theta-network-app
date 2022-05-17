<?php

namespace App\Console\Commands;

use App\Models\TfuelSupply;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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
    public function handle()
    {
        $response = Http::get('https://explorer.thetatoken.org:8443/api/supply/tfuel');
        if ($response->ok()) {
            $data = $response->json();
            TfuelSupply::updateOrCreate(
                ['date' => Carbon::today()],
                ['supply' => $data['circulation_supply']]
            );
        }
        $this->info('Done');
        return 0;
    }
}
