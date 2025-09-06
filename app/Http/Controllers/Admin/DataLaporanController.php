<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Profil;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DataLaporanController extends Controller
{
    /**
     * Menampilkan laporan gaji lengkap dengan perhitungan jam kerja & lembur secara real-time.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function tampilkanLaporanGaji(Request $request)
    {
        // Ambil periode dari request, jika tidak ada gunakan bulan & tahun saat ini
        // Controller ini akan merespons filter AJAX, jadi kita ambil dari query string
        $periode = $request->query('periode', Carbon::now()->format('Y-m'));
        $date = Carbon::parse($periode);
        $selectedYear = $date->year;
        $selectedMonth = $date->month;

        // 1. Ambil semua data presensi 'masuk' pada periode yang dipilih
        $semuaPresensiMasuk = Presensi::with('activity')
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->where('status', 'masuk')
            ->orderBy('id_user')
            ->orderBy('created_at')
            ->get();

        // Kelompokkan data presensi berdasarkan ID user
        $presensiByUser = $semuaPresensiMasuk->groupBy('id_user');
        $userIdsWithPresensi = $presensiByUser->keys();
        
        $laporanData = [];

        if ($userIdsWithPresensi->isNotEmpty()) {
            // 2. Ambil data user yang relevan beserta profil dan data gaji yang MUNGKIN sudah ada
            $users = User::with(['profil', 'gaji' => function ($query) use ($selectedYear, $selectedMonth) {
                $query->whereYear('tanggal_penggajian', $selectedYear)
                      ->whereMonth('tanggal_penggajian', $selectedMonth)
                      ->with('slipGaji');
            }])->find($userIdsWithPresensi);

            // 3. Lakukan proses perhitungan untuk setiap user
            foreach ($users as $user) {
                $userId = $user->id_user;
                $totalNormalHours = 0;
                $totalOvertimeHours = 0;

                // Pastikan ada data presensi untuk user ini sebelum melanjutkan
                if (!isset($presensiByUser[$userId])) {
                    continue;
                }
                
                // Loop melalui setiap presensi 'masuk' milik user ini
                foreach ($presensiByUser[$userId] as $presensiMasuk) {
                    if (!$presensiMasuk->activity) continue;

                    // Cari 'keluar' yang cocok
                    $presensiKeluar = Presensi::where('id_user', $userId)
                        ->where('id_activity', $presensiMasuk->id_activity)
                        ->where('status', 'keluar')
                        ->where('created_at', '>', $presensiMasuk->created_at)
                        ->orderBy('created_at', 'asc')
                        ->first();

                    if ($presensiKeluar) {
                        $durationInMinutes = $presensiKeluar->created_at->diffInMinutes($presensiMasuk->created_at);
                        $durationInHours = $durationInMinutes / 60;

                        // Logika Perhitungan Jam Normal & Lembur Harian
                        $normalHoursThreshold = 8;
                        if ($durationInHours > $normalHoursThreshold) {
                            $totalNormalHours += $normalHoursThreshold;
                            $totalOvertimeHours += ($durationInHours - $normalHoursThreshold);
                        } else {
                            $totalNormalHours += $durationInHours;
                        }
                    }
                }

                // 4. Siapkan struktur data final untuk dikirim ke view
                $gajiRecord = $user->gaji->first();
                $slipGaji = $gajiRecord ? $gajiRecord->slipGaji : null;

                // Gabungkan data hasil perhitungan dengan data dari slip gaji (jika ada)
                $laporanData[] = [
                    'id_user' => $user->id_user,
                    'nama' => $user->nama,
                    'profil' => $user->profil,
                    'gaji' => [
                        [ // Struktur array ini dibuat agar cocok dengan JS di view
                            'total_jam' => round($totalNormalHours + $totalOvertimeHours, 2),
                            'total_jam_lembur' => round($totalOvertimeHours, 2), // DATA LEMBUR BARU!
                            'slip_gaji' => $slipGaji // Kirim data slip jika sudah ada
                        ]
                    ]
                ];
            }
        }

        // 5. Ambil daftar status pekerjaan yang unik dari tabel profil
        $jobStatuses = Profil::whereNotNull('detail_pekerjaan')
            ->where('detail_pekerjaan', '!=', '')
            ->distinct()
            ->pluck('detail_pekerjaan');
            
        // 6. Kirim data yang sudah dihitung ke view
        return view('Admin.DataLaporan', [
            'usersJson' => json_encode($laporanData),
            'jobStatuses' => $jobStatuses
        ]);
    }
}