<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PegawaiActivity; 
use Illuminate\Support\Carbon; 
use Illuminate\Support\Facades\DB; 
use SimpleSoftwareIO\QrCode\Facades\QrCode; 

class PegawaiActivitiesSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     */
    public function run(): void
    {
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        PegawaiActivity::truncate();

        $activitiesData = [
            [
                'nama_aktivitas' => 'Rapat Mingguan Divisi IT',
                'deskripsi' => 'Pembahasan progres proyek Q2 dan perencanaan sprint berikutnya.',
                'jam_mulai' => Carbon::now()->subHours(2), 
                'jam_selesai' => Carbon::now()->subHour(), 
            ],
            [
                'nama_aktivitas' => 'Sesi Pelatihan Karyawan Baru',
                'deskripsi' => 'Pelatihan dasar penggunaan sistem internal perusahaan.',
                'jam_mulai' => Carbon::parse('2025-07-01 09:00:00'), 
                'jam_selesai' => Carbon::parse('2025-07-01 12:00:00'),
            ],
            [
                'nama_aktivitas' => 'Kegiatan Sosial Bersih-Bersih Lingkungan',
                'deskripsi' => 'Aktivitas sukarela membersihkan area sekitar kantor.',
                'jam_mulai' => Carbon::parse('2025-06-25 14:00:00'),
                'jam_selesai' => Carbon::parse('2025-06-25 16:00:00'),
            ],
        ];

        $qrCodeStoragePath = public_path('qrcodes');
        if (!file_exists($qrCodeStoragePath)) {
            mkdir($qrCodeStoragePath, 0777, true); 
        }

        foreach ($activitiesData as $activityData) {
            $activity = PegawaiActivity::create($activityData);

          
            $qrCodeContent = json_encode([ 
                'uuid' => $activity->uuid,
                'nama_aktivitas' => $activity->nama_aktivitas,
                'deskripsi' => $activity->deskripsi,
                'jam_mulai' => $activity->jam_mulai->format('Y-m-d H:i:s'),
                'jam_selesai' => $activity->jam_selesai->format('Y-m-d H:i:s'),
            ]);

   
            $qrCodeFileName = 'qrcodes/' . $activity->uuid . '.svg';
            $qrCodeFullPath = public_path($qrCodeFileName);

            QrCode::size(200) 
                  ->format('svg') 
                  ->errorCorrection('H') 
                  ->generate($qrCodeContent, $qrCodeFullPath); 

            $activity->update(['qrcode_path' => $qrCodeFileName]);
        }

      
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Pegawai Activities seeded!');
    }
}
