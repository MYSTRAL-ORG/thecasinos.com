<?php

namespace App\Console;

use App\Console\Commands\CasinoCrawler;
use App\Console\Commands\CleanCasinoDetailsOpenAi;

use App\Console\Commands\generateSiteMapCasino;
use App\Console\Commands\IndexUrls;
use App\Console\Commands\IndexUrlsBing;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

       // $schedule->command(IndexUrls::class)->dailyAt('14:30')->timezone('Indian/Reunion')->between('14:28', '14:32');
       // $schedule->command(IndexUrlsBing::class)->everySixHours();

        //Lance la géneration du site tous les week end
       //$schedule->command(generateSiteMapCasino::class)->weekly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}


