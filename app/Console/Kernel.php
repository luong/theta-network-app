<?php

namespace App\Console;

use App\Console\Commands\MonitorTransactions;
use App\Console\Commands\TweetDailyUpdates;
use App\Console\Commands\UpdatePrices;
use App\Console\Commands\UpdateStakes;
use App\Console\Commands\UpdateDailyStats;
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
        $schedule->command(UpdateDailyStats::class)->everySixHours();
        $schedule->command(UpdateStakes::class)->hourly();
        $schedule->command(UpdatePrices::class)->everyFiveMinutes();
        $schedule->command(MonitorTransactions::class)->everyTwoMinutes();
        $schedule->command(TweetDailyUpdates::class)->dailyAt('00:30');
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
