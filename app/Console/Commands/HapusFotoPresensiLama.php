<?php
// app/Console/Commands/HapusFotoPresensiLama.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Presensi; // Ganti dengan model Presensi Anda
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class HapusFotoPresensiLama extends Command
{
    protected $signature = 'presensi:hapus-foto-lama';
    protected $description = 'Hapus file foto presensi yang lebih tua dari satu hari';

    public function handle()
    {
        $this->info('Mulai menghapus foto presensi lama...');

        $presensiLama = Presensi::where('created_at', '<', Carbon::today())
                                ->whereNotNull('path_foto')
                                ->get();

        if ($presensiLama->isEmpty()) {
            $this->info('Tidak ada foto lama untuk dihapus.');
            return;
        }

        foreach ($presensiLama as $presensi) {
            if (Storage::disk('public')->exists($presensi->path_foto)) {
                Storage::disk('public')->delete($presensi->path_foto);
                $this->line('Menghapus: ' . $presensi->path_foto);

                $presensi->path_foto = null;
                $presensi->save();
            }
        }

        $this->info('Selesai. ' . $presensiLama->count() . ' foto telah dihapus.');
    }
}