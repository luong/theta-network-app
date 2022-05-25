<?php

namespace App\Console\Commands;

use App\Models\NodeValidator;
use App\Services\ThetaService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateThetaStakes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:updateThetaStakes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update theta stakes';

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
        $response = Http::get('https://explorer.thetatoken.org:8443/api/stake/all');
        if (!$response->ok()) {
            $this->error('Request failed.');
            return 0;
        }

        $stakes = $response->json()['body'];

        $validators = [];
        foreach ($stakes as $stake) {
            if ($stake['type'] == 'vcp') {
                if (!isset($validators[$stake['holder']])) {
                    $validators[$stake['holder']] = ['amount' => 0];
                }
                $validators[$stake['holder']]['amount'] += ($stake['amount'] / 1000000000000000000);
            }
        }

        $holders = $thetaService->getHolders();
        if (!empty($validators)) {
            NodeValidator::truncate();
            $data = [];
            foreach ($validators as $holder => $props) {
                $node = ['holder' => $holder, 'name' => '', 'chain' => 'theta', 'amount' => $props['amount'], 'coin' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
                if (isset($holders[$holder])) {
                    $node['name'] = $holders[$holder]['name'];
                }
                $data[] = $node;
            }
            NodeValidator::insert($data);
            $thetaService->cacheValidators();
            $thetaService->updateDailyValidators(count($data));
        }

        $this->info('Done.');
        return 0;
    }
}
