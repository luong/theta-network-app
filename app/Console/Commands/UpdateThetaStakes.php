<?php

namespace App\Console\Commands;

use App\Models\Holder;
use App\Models\NodeValidator;
use App\Services\ThetaService;
use App\Services\TweetService;
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
    public function handle(ThetaService $thetaService, TweetService $tweetService)
    {
        $response = Http::get('https://explorer.thetatoken.org:8443/api/stake/all');
        if (!$response->ok()) {
            $this->error('Request failed.');
            return 0;
        }

        $stakes = $response->json()['body'];
        $networkInfo = $thetaService->getNetworkInfo();

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
            $validatorCount = count($validators);
            $data = [];
            foreach ($validators as $holder => $props) {
                $node = ['holder' => $holder, 'name' => '*', 'chain' => 'theta', 'amount' => $props['amount'], 'coin' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
                if (isset($holders[$holder])) {
                    $node['name'] = $holders[$holder]['name'];
                } else {
                    $tweet = "[Bot] We're thrilled to have a new validator joining @Theta_Network : " . number_format($props['amount']) . " \$THETA https://explorer.thetatoken.org/account/{$holder}";
                    $tweetService->tweetText($tweet);

                    Holder::updateOrCreate(
                        ['code' => $holder, 'chain' => 'theta'],
                        ['name' => '*']
                    );
                    $thetaService->cacheHolders();
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
