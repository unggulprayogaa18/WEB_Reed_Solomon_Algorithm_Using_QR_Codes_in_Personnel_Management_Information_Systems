<?php

namespace App\Http\Controllers;

// ... use statement lainnya
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

class ActivityController extends Controller
{

    /**
     * Membuat data presensi 'keluar' untuk semua yang belum checkout hari ini.
     */
    public function endDayAttendance(): RedirectResponse
    {
        $today = Carbon::today();
        $checkoutCount = 0;

        $presensiMasukHariIni = Presensi::where('status', 'masuk')
            ->whereDate('created_at', $today)
            ->get();

        foreach ($presensiMasukHariIni as $masuk) {
            $sudahKeluar = Presensi::where('id_user', $masuk->id_user)
                ->where('id_activity', $masuk->id_activity)
                ->where('status', 'keluar')
                ->whereDate('created_at', $today)
                ->exists();

            if (!$sudahKeluar) {
                Presensi::create([
                    'id_user' => $masuk->id_user,
                    'id_activity' => $masuk->id_activity,
                    'status' => 'keluar',

                ]);
                $checkoutCount++;
            }
        }

        if ($checkoutCount > 0) {
            return redirect()->back()->with('success', "$checkoutCount pegawai berhasil di-presensi keluar secara otomatis.");
        }

        return redirect()->back()->with('info', 'Tidak ada pegawai yang perlu di-presensi keluar. Semua sudah lengkap.');
    }
}