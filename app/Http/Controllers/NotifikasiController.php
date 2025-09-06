<?php

namespace App\Http\Controllers;

// Import model User, karena kita akan mengirim notifikasi ke semua user
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotifikasiController extends Controller
{
    /**
     * Mengirimkan notifikasi pengingat umum untuk melakukan aktivitas.
     * Fungsi ini tidak lagi memeriksa presensi, hanya mengirim broadcast.
     */
    public function kirimPengingatAktivitas()
    {
        Log::info('Method kirimPengingatAktivitas() berhasil dijalankan.');

        // MODIFICATION: Added ->where('jabatan', 'pegawai') to only get users with the 'pegawai' role.
        $users = User::where('jabatan', 'pegawai')
            ->whereNotNull('no_telepon')
            ->where('no_telepon', '!=', '')
            ->get();

        if ($users->isEmpty()) {
            Log::warning('Tidak ditemukan user dengan role "pegawai" dan memiliki nomor telepon untuk dikirimi notifikasi.');
            return response()->json(['message' => 'Tidak ada target notifikasi untuk role pegawai.'], 404);
        }

        Log::info("Ditemukan {$users->count()} user (pegawai) yang akan dikirimi pengingat.");

        $sentCount = 0;
        $results = [];

        foreach ($users as $user) {
            // Assuming kirimNotifikasiUmumFonnte is the method to send the notification
            $this->kirimNotifikasiUmumFonnte($user);
            $sentCount++;
            $results[] = "Notifikasi pengingat terkirim ke: " . $user->nama . " (Role: " . $user->jabatan . ")";
        }

        Log::info("Proses pengiriman pengingat selesai. Total terkirim: {$sentCount}.");

        return response()->json([
            'message' => 'Proses pengiriman pengingat umum untuk pegawai selesai.',
            'notifikasi_terkirim' => $sentCount,
            'detail' => $results
        ]);
    }

    /**
     * Fungsi terpisah untuk mengirim pesan umum via Fonnte
     * @param User $user
     */
    private function kirimNotifikasiUmumFonnte(User $user)
    {
        // Token Fonnte Anda
        $token = 'aUNu6q88rhW5ZycH5L7m';

        $targetNumber = $user->no_telepon;

        $hour = now()->hour;
        $greeting = "Selamat Pagi";
        if ($hour >= 11 && $hour < 15) {
            $greeting = "Selamat Siang";
        } elseif ($hour >= 15) {
            $greeting = "Selamat Sore";
        }

        $message = "{$greeting}, {$user->nama}!\n\nIni adalah pengingat untuk tidak lupa melakukan aktivitas dan presensi Anda hari ini. Tetap semangat!";

        Http::withHeaders([
            'Authorization' => $token,
        ])->post('https://api.fonnte.com/send', [
                    'target' => $targetNumber,
                    'message' => $message,
                ]);
    }
}