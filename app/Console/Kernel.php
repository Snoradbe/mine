<?php

namespace App\Console;

use App\Console\Cron\Cabinet;
use App\Console\Cron\CabinetNotifications;
use App\Console\Cron\ServersMonitoring;
use App\Console\Cron\Shop;
use App\Console\Cron\Votes;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Cabinet::class,
        CabinetNotifications::class,
        ServersMonitoring::class,
        Shop::class,
        Votes::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('rm:cron:monitoring')->everyFiveMinutes();
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
