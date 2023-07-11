<?php

namespace App\Console;

use App\Actions\ImportItemLocations;
use App\Actions\ImportItems;
use App\Actions\ImportLocations;
use App\Actions\ImportTransactions;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        ImportTransactions::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            ImportLocations::run();
            ImportItems::run();
            ImportItemLocations::run();
            ImportTransactions::run();
        })->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
