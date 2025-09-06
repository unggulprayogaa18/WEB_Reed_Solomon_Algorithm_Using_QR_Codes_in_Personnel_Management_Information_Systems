<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

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
        // Jadwal yang sudah ada untuk notifikasi
        $schedule->call(function () {
            Log::info('Scheduler berjalan: Memanggil NotifikasiController@kirimPengingatAktivitas...');
            $controller = app(\App\Http\Controllers\NotifikasiController::class);
            $controller->kirimPengingatAktivitas();
        })->cron('0 7,12,16,20 * * *');

        // Jadwal yang sudah ada untuk hapus foto lama
        $schedule->command('presensi:hapus-foto-lama')->dailyAt('00:00');

        // --- TAMBAHKAN JADWAL BARU DI SINI ---
        // Menjalankan perintah penghapusan data lama setiap tahun
        // pada tanggal 1 Januari, jam 2 pagi.
        $schedule->command('app:prune-old-records')->yearly()->at('02:00');
    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
