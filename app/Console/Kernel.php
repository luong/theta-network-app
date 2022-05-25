<?php

namespace App\Console;

use App\Console\Commands\MonitorThetaTransactions;
use App\Console\Commands\UpdatePrices;
use App\Console\Commands\UpdateThetaStakes;
use App\Console\Commands\UpdateThetaStats;
use App\Console\Commands\UpdateThetaValidators;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(UpdateThetaStats::class)->everySixHours();
        $schedule->command(UpdateThetaStakes::class)->everySixHours();
        $schedule->command(UpdatePrices::class)->everyFiveMinutes();
        $schedule->command(MonitorThetaTransactions::class)->everyTwoMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
