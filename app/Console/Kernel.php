<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule notifikasi presensi mahasiswa setiap menit
        $schedule->command('presensi:notify-mahasiswa')
            ->everyFiveMinutes()
            ->appendOutputTo(storage_path('logs/scheduler.log'));

        // Schedule inspire command (opsional)
        $schedule->command('inspire')
            ->everyMinute()
            ->appendOutputTo(storage_path('logs/scheduler.log'));
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