<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Gaji;
use App\Models\Presensi; // PERLU DI-IMPORT
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SlipGajiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $daftarGaji = Gaji::with('user', 'slipGaji')
            ->where('id_user', $user->id_user)
            ->orderBy('tanggal_penggajian', 'desc')
            ->paginate(10);

        // Loop untuk menambahkan data aktivitas dan jam lembur
        foreach ($daftarGaji as $gaji) {
            $periode = $gaji->tanggal_penggajian;
            $startDate = $periode->copy()->startOfMonth();
            $endDate = $periode->copy()->endOfMonth();

            // --- Menghitung Jumlah Aktivitas yang Dihadiri ---
            $attendedActivitiesCount = DB::table('presensi')
                ->select(DB::raw('DATE(created_at) as attendance_date'), 'id_activity')
                ->where('id_user', $user->id_user)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', ['masuk', 'keluar'])
                ->groupBy('attendance_date', 'id_activity')
                ->havingRaw('COUNT(DISTINCT status) = 2')
                ->get()
                ->count();
            
            $gaji->aktivitas_dihadiri = $attendedActivitiesCount;

            // --- BLOK BARU: Menghitung Total Jam Lembur ---
            $totalOvertimeHours = 0;
            $presensiMasukBulanIni = Presensi::where('id_user', $user->id_user)
                ->where('status', 'masuk')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            foreach ($presensiMasukBulanIni as $presensiMasuk) {
                $presensiKeluar = Presensi::where('id_user', $user->id_user)
                    ->where('id_activity', $presensiMasuk->id_activity)
                    ->where('status', 'keluar')
                    ->where('created_at', '>', $presensiMasuk->created_at)
                    ->orderBy('created_at', 'asc')
                    ->first();
                
                if ($presensiKeluar) {
                    $durationInMinutes = $presensiKeluar->created_at->diffInMinutes($presensiMasuk->created_at);
                    $durationInHours = $durationInMinutes / 60;
                    $normalHoursThreshold = 8;

                    if ($durationInHours > $normalHoursThreshold) {
                        $totalOvertimeHours += ($durationInHours - $normalHoursThreshold);
                    }
                }
            }
            $gaji->total_jam_lembur = round($totalOvertimeHours, 2);
            // --- AKHIR BLOK BARU ---
        }

        return view('Pegawai.SlipGaji', compact('daftarGaji'));
    }
}