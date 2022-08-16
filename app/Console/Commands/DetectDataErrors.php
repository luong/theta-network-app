<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Mail\DataErrorEmail;
use App\Services\SystemService;
use App\Services\ThetaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DetectDataErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:detectDataErrors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect data errors';

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
    public function handle(ThetaService $thetaService, SystemService $systemService)
    {
        $commandTrackers = $thetaService->getCommandTrackers();
        $commands = ['DailyStats', 'Stakes', 'Prices', 'Drops', 'DailyTweet2', 'Transactions', 'News', 'Blocks', 'Accounts', 'Whales'];
        $today = date('Y-m-d');

        $commandsFailed = [];
        foreach ($commands as $command) {
            if (!isset($commandTrackers[$command])) {
                $commandsFailed[] = $command;
                continue;
            }
            $lastDate = date('Y-m-d', $commandTrackers[$command]['last_run']);
            if (empty($commandTrackers[$command]['last_run'] || $lastDate != $today)) {
                $commandsFailed[] = $command;
                continue;
            }
        }

        if (!empty($commandsFailed)) {
            Mail::to(Constants::ADMIN_EMAIL)->send(new DataErrorEmail(['date' => $today, 'commands' => $commandsFailed]));
        }

        return 0;
    }
}
