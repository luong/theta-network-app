<?php

namespace App\Console\Commands;

use App\Services\ThetaService;
use Illuminate\Console\Command;

class Start extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize the project';

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
        $thetaService->caching();
        return 0;
    }
}
