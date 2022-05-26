<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Helpers\Helper;
use App\Models\Holder;
use App\Models\NodeValidator;
use App\Services\ThetaService;
use App\Services\TweetService;
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
    public function handle(ThetaService $thetaService, TweetService $tweetService)
    {
        $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/stake/all');
        if (!$response->ok()) {
            $this->error('Request failed.');
            return 0;
        }

        $stakes = $response->json()['body'];
        $networkInfo = $thetaService->getNetworkInfo();
        $cachedValidators = $thetaService->getValidators();

        $validators = [];
        foreach ($stakes as $stake) {
            if ($stake['type'] == 'vcp') {
                if (!isset($validators[$stake['holder']])) {
                    $validators[$stake['holder']] = ['amount' => 0];
                }
                $validators[$stake['holder']]['amount'] += ($stake['amount'] / Constants::THETA_WEI);
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
                } else {
                    $tweet = "[Bot] We're thrilled to have a new validator joining @Theta_Network : " . number_format($node['amount']) . " \$THETA " . Helper::makeThetaAccountURL($holder);
                    $tweetService->tweetText($tweet);

                    Holder::updateOrCreate(
                        ['code' => $holder, 'chain' => 'theta'],
                        ['name' => '*']
                    );
                    $thetaService->cacheHolders();
                }

                if (round($node['amount']) != round($cachedValidators[$holder]['amount'])) {
                    $tweet = "[Bot] A validator updated its \$THETA amount from " . number_format($cachedValidators[$holder]['amount']) . ' to ' . number_format($node['amount']) . ' ' . Helper::makeThetaAccountURL($holder);
                    $tweetService->tweetText($tweet);
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
