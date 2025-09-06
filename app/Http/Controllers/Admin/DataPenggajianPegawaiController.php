<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gaji;
use App\Models\PegawaiActivity;
use App\Models\Presensi;
use App\Models\Profil;
use App\Models\SlipGaji;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPenggajianPegawaiController extends Controller
{
    /**
     * Menampilkan halaman rekapitulasi dan data penggajian.
     */
    public function index(Request $request)
    {
        $selectedYear = $request->input('year', Carbon::now()->year);
        $selectedMonth = $request->input('month', Carbon::now()->month);

        // 1. Ambil semua data 'masuk' pada periode yang dipilih
        $semuaPresensiMasuk = Presensi::with('activity')
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->where('status', 'masuk')
            ->orderBy('id_user')
            ->orderBy('created_at')
            ->get();

        // Kelompokkan data 'masuk' berdasarkan ID user
        $presensiByUser = $semuaPresensiMasuk->groupBy('id_user');
        $userIdsWithPresensi = $presensiByUser->keys();
        $rekapPresensi = [];

        if ($userIdsWithPresensi->isNotEmpty()) {
            // Ambil data user yang relevan dalam satu query
            $users = User::with(['profil', 'gaji' => function ($query) use ($selectedYear, $selectedMonth) {
                $query->whereYear('tanggal_penggajian', $selectedYear)
                      ->whereMonth('tanggal_penggajian', $selectedMonth)
                      ->with('slipGaji');
            }])->find($userIdsWithPresensi);

            foreach ($users as $user) {
                $userId = $user->id_user;
                $totalNormalHours = 0;
                $totalOvertimeHours = 0;
                $completedSessionDates = [];
                $dailyDetailsForUser = [];

                // 2. Proses setiap sesi 'masuk' untuk user ini
                foreach ($presensiByUser[$userId] as $presensiMasuk) {
                    if (!$presensiMasuk->activity) continue;

                    // Cari 'masuk' berikutnya sebagai batas atas
                    $batasAtas = Presensi::where('id_user', $userId)
                        ->where('id_activity', $presensiMasuk->id_activity)
                        ->where('status', 'masuk')
                        ->where('created_at', '>', $presensiMasuk->created_at)
                        ->min('created_at');

                    // Cari 'keluar' yang cocok dalam rentang waktu yang terisolasi
                    $queryKeluar = Presensi::where('id_user', $userId)
                        ->where('id_activity', $presensiMasuk->id_activity)
                        ->where('status', 'keluar')
                        ->where('created_at', '>', $presensiMasuk->created_at)
                        ->orderBy('created_at', 'asc');

                    if ($batasAtas) {
                        $queryKeluar->where('created_at', '<', $batasAtas);
                    }
                    $presensiKeluar = $queryKeluar->first();

                    $detail = [
                        'date' => $presensiMasuk->created_at->toDateString(),
                        'nama_aktivitas' => $presensiMasuk->activity->nama_aktivitas,
                        'jam_masuk' => $presensiMasuk->created_at->format('H:i'),
                        'jam_keluar' => null,
                        'durasi_jam' => 0,
                        'jam_lembur_harian' => 0,
                    ];

                    if ($presensiKeluar) {
                        $durationInMinutes = $presensiKeluar->created_at->diffInMinutes($presensiMasuk->created_at);
                        $durationInHours = $durationInMinutes / 60;

                        // Logika Perhitungan Jam Normal & Lembur Harian
                        $normalHoursThreshold = 8; // Jam normal mutlak 8 jam per hari
                        if ($durationInHours > $normalHoursThreshold) {
                            $dailyNormalHours = $normalHoursThreshold;
                            $dailyOvertimeHours = $durationInHours - $normalHoursThreshold;
                        } else {
                            $dailyNormalHours = $durationInHours;
                            $dailyOvertimeHours = 0;
                        }
                        $totalNormalHours += $dailyNormalHours;
                        $totalOvertimeHours += $dailyOvertimeHours;

                        // Tandai tanggal ini sebagai hari kerja yang valid
                        $completedSessionDates[$presensiMasuk->created_at->toDateString()] = true;

                        $detail['jam_keluar'] = $presensiKeluar->created_at->format('H:i');
                        $detail['durasi_jam'] = round($durationInHours, 2);
                        $detail['jam_lembur_harian'] = round($dailyOvertimeHours, 2); // Simpan nilai lembur harian
                    }
                    $dailyDetailsForUser[] = $detail;
                }

                $gajiRecord = $user->gaji->first();
                $rekapPresensi[$userId] = [
                    'nama_user' => $user->nama,
                    'has_profile' => !is_null($user->profil),
                    'detail_pekerjaan' => optional($user->profil)->detail_pekerjaan ?? 'Profil Belum Diatur',
                    'tipe_gaji' => optional($user->profil)->tipe_gaji ?? 'bulanan',
                    'tarif_gaji_default' => optional($user->profil)->tarif_gaji ?? 0,
                    'total_jam_kerja_terhitung' => round($totalNormalHours + $totalOvertimeHours, 2),
                    'total_jam_normal_terhitung' => round($totalNormalHours, 2),     // DATA BARU
                    'total_jam_lembur_terhitung' => round($totalOvertimeHours, 2),    // DATA BARU
                    'total_hari_kerja_terhitung' => count($completedSessionDates), // Hitung hari unik
                    'daily_details' => $dailyDetailsForUser,
                    'existing_salary' => false,
                    'calculated_total_gaji' => 0,
                ];

                if ($gajiRecord && $gajiRecord->slipGaji) {
                    $rekapPresensi[$userId]['existing_salary'] = true;
                    $rekapPresensi[$userId]['calculated_total_gaji'] = $gajiRecord->slipGaji->total_gaji;
                }
            }
        }

        return view('Admin.DataPenggajian', compact('rekapPresensi', 'selectedYear', 'selectedMonth'));
    }


    public function calculateSalary(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id_user',
            'total_monthly_hours' => 'required|numeric|min:0',
            'total_days_attended' => 'required|numeric|min:0',
            'month' => 'required|numeric|min:1|max:12',
            'year' => 'required|numeric|min:2000',
            'tipe_pembayaran' => 'required|in:bulanan,harian,per_jam',
            'tarif_gaji' => 'required|numeric|min:0',
            'tunjangan' => 'required|numeric|min:0',
        ]);

        $userId = $request->input('user_id');
        $totalMonthlyHours = $request->input('total_monthly_hours');
        $totalDaysAttended = $request->input('total_days_attended');
        $tipePembayaran = $request->input('tipe_pembayaran');
        $tarifGaji = $request->input('tarif_gaji');
        $tunjangan = $request->input('tunjangan');

        $gajiBerdasarkanTipe = 0;
        
        switch ($tipePembayaran) {
            case 'per_jam':
                $gajiBerdasarkanTipe = $totalMonthlyHours * $tarifGaji;
                break;
            case 'harian':
                $gajiBerdasarkanTipe = $totalDaysAttended * $tarifGaji;
                break;
            case 'bulanan':
                $gajiPokokBulanan = $tarifGaji;
                $hariKerjaSebulan = Carbon::create($request->year, $request->month)->daysInMonth;
                $hariAbsen = $hariKerjaSebulan - $totalDaysAttended;
                
                if ($hariKerjaSebulan > 0 && $hariAbsen > 0) {
                    $tarifHarianDariBulanan = $gajiPokokBulanan / $hariKerjaSebulan;
                    $potonganAbsen = $hariAbsen * $tarifHarianDariBulanan;
                    $gajiBerdasarkanTipe = $gajiPokokBulanan - $potonganAbsen;
                } else {
                    $gajiBerdasarkanTipe = $gajiPokokBulanan;
                }
                break;
        }

        $totalGaji = $gajiBerdasarkanTipe + $tunjangan;

        $tanggalPenggajian = Carbon::create($request->year, $request->month, 1)->toDateString();

        DB::transaction(function () use ($userId, $tanggalPenggajian, $totalMonthlyHours, $tipePembayaran, $gajiBerdasarkanTipe, $tunjangan, $totalGaji, $request) {
            $gaji = Gaji::updateOrCreate(
                ['id_user' => $userId, 'tanggal_penggajian' => $tanggalPenggajian],
                ['total_jam' => $totalMonthlyHours, 'status_persetujuan' => 'disetujui']
            );

            SlipGaji::updateOrCreate(
                ['id_gaji' => $gaji->id_gaji],
                [
                    'periode' => Carbon::create($request->year, $request->month, 1)->format('Y-m'),
                    'tipe_pembayaran' => $tipePembayaran,
                    'gaji_berdasarkan_tipe' => $gajiBerdasarkanTipe,
                    'tunjangan' => $tunjangan,
                    'total_gaji' => $totalGaji,
                ]
            );
        });
        
        $user = User::find($userId);
        return redirect()->back()->with('success', "Gaji untuk {$user->nama} berhasil dihitung: Rp " . number_format($totalGaji, 2, ',', '.'));
    }
}