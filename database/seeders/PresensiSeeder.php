<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Presensi; // Import model Presensi
use App\Models\User; // Import model User
use App\Models\PegawaiActivity; // Import model PegawaiActivity
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB; // Import Facade DB

class PresensiSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Presensi::truncate();

        $pegawaiUser = User::where('jabatan', 'pegawai')->first();
        $activity1 = PegawaiActivity::first(); 

        if ($pegawaiUser && $activity1) {
            Presensi::create([
                'id_user' => $pegawaiUser->id_user,
                'id_activity' => $activity1->id_activity,
                'status' => 'masuk',
                'created_at' => Carbon::now()->subMinutes(30), 
                'updated_at' => Carbon::now()->subMinutes(30),
            ]);

            Presensi::create([
                'id_user' => $pegawaiUser->id_user,
                'id_activity' => $activity1->id_activity,
                'status' => 'keluar',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $this->command->info('Presensi seeded!');
        } else {
            $this->command->warn('Skipping PresensiSeeder: User atau PegawaiActivity tidak ditemukan. Pastikan seeder User dan PegawaiActivity dijalankan terlebih dahulu.');
        }


        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

