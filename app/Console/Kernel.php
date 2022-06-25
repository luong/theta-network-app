<?php

namespace App\Console;

use App\Console\Commands\Accounts;
use App\Console\Commands\Blocks;
use App\Console\Commands\Drops;
use App\Console\Commands\Transactions;
use App\Console\Commands\Stakes;
use App\Console\Commands\DailyTweet;
use App\Console\Commands\Prices;
use App\Console\Commands\MonitorStakes;
use App\Console\Commands\DailyStats;
use App\Console\Commands\Whales;
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
        $schedule->command(DailyStats::class)->everySixHours();
        $schedule->command(Stakes::class)->everyFiveMinutes();
        $schedule->command(Prices::class)->everyFiveMinutes();
        $schedule->command(Transactions::class)->everyTwoMinutes();
        $schedule->command(Blocks::class)->everyMinute();
        $schedule->command(Drops::class)->everyMinute();
        $schedule->command(DailyTweet::class)->dailyAt('00:30');
        $schedule->command(Accounts::class)->dailyAt('01:00');
        $schedule->command(Whales::class)->dailyAt('10:00');
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
