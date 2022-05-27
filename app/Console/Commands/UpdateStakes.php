<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Helpers\Helper;
use App\Models\Holder;
use App\Models\NodeValidator;
use App\Services\ThetaService;
use App\Services\MessageService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateStakes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:updateStakes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stakes';

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
    public function handle(ThetaService $thetaService, MessageService $messageService)
    {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/stake/all');
        if (!$response->ok()) {
            $this->error('Request failed.');
            return 0;
        }

        $stakes = $response->json()['body'];
        $cachedValidators = $thetaService->getValidators();

        $validators = [];
        foreach ($stakes as $stake) {
            if ($stake['type'] == 'vcp') {
                if (!isset($validators[$stake['holder']])) {
                    $validators[$stake['holder']] = ['amount' => 0];
                }
                $validators[$stake['holder']]['amount'] += round($stake['amount'] / Constants::THETA_WEI);
            }
        }

        $holders = $thetaService->getHolders();
        if (!empty($validators)) {
            NodeValidator::truncate();
            $validatorCount = count($validators);
            $data = [];
            foreach ($validators as $holder => $props) {
                $node = ['holder' => $holder, 'name' => '*', 'chain' => 'theta', 'amount' => round($props['amount']), 'coin' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
                if (isset($holders[$holder])) {
                    $node['name'] = $holders[$holder]['name'];
                }

                if (!isset($cachedValidators[$holder])) {  // New validator
                    $messageService->hasNewValidator($holder, number_format($node['amount']));

                    Holder::updateOrCreate(
                        ['code' => $holder, 'chain' => 'theta'],
                        ['name' => 'Validator']
                    );
                    $thetaService->cacheHolders();
                }

                if (round($node['amount']) != round($cachedValidators[$holder]['amount'])) {
                    $messageService->validatorChangesStakes($holder, number_format($cachedValidators[$holder]['amount']), number_format($node['amount']));
                }

                $data[] = $node;
            }
            NodeValidator::insert($data);

            $thetaService->cacheValidators();
            $thetaService->updateDailyValidators($validatorCount);
            $thetaService->cacheNetworkInfo();
        }

        $this->info('Done.');
        return 0;
    }
}