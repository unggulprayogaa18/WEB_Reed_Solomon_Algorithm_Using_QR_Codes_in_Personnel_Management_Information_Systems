<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gaji; // Pastikan model Gaji di-import
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PruneOldRecords extends Command
{
    /**
     * Nama dan signature dari console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-old-records';

    /**
     * Deskripsi dari console command.
     *
     * @var string
     */
    protected $description = 'Hapus data gaji dan slip gaji yang lebih lama dari dua tahun';

    /**
     * Jalankan console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Memulai proses penghapusan data lama...');
        Log::info('Tugas Terjadwal: Memulai command PruneOldRecords.');

        // Tentukan tanggal batas (2 tahun yang lalu dari sekarang)
        $cutoffDate = Carbon::now()->subYears(2);

        // Cari data Gaji yang lebih lama dari tanggal batas.
        // Asumsi: Menghapus record 'Gaji' akan otomatis menghapus 'SlipGaji'
        // yang terkait jika Anda sudah mengatur 'ON DELETE CASCADE' pada foreign key di database.
        // Jika tidak, Anda harus menghapus SlipGaji secara manual terlebih dahulu.
        $oldRecords = Gaji::where('tanggal_penggajian', '<', $cutoffDate);

        $count = $oldRecords->count();

        if ($count > 0) {
            $this->info("Ditemukan {$count} record yang lebih lama dari {$cutoffDate->format('Y-m-d')} untuk dihapus.");
            Log::info("Ditemukan {$count} record gaji untuk dihapus.");

            // Hapus data yang ditemukan
            $oldRecords->delete();

            $this->info("Berhasil menghapus {$count} record data gaji lama.");
            Log::info("Berhasil menghapus {$count} record data gaji lama.");
        } else {
            $this->info('Tidak ada data lama yang ditemukan untuk dihapus.');
            Log::info('Tidak ada data lama yang ditemukan untuk dihapus.');
        }

        $this->info('Proses penghapusan data selesai.');
        Log::info('Tugas Terjadwal: Selesai menjalankan command PruneOldRecords.');

        return 0; // Mengembalikan 0 menandakan sukses
    }
}