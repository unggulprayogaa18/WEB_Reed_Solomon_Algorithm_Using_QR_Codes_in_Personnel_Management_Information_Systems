<?php

namespace App\Http\Controllers\Pemimpin;

use App\Http\Controllers\Controller;
use App\Models\IzinSakit;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PemimpinController extends Controller
{
    /**
     * Menampilkan halaman Laporan Gabungan.
     * Secara default, akan mengirimkan data untuk Laporan Gaji.
     * Data Kehadiran akan diambil via Fetch API.
     */
/**
     * Menampilkan halaman Laporan Gabungan.
     * Secara default, akan mengirimkan data untuk Laporan Gaji.
     * Data Kehadiran akan diambil via Fetch API.
     */
    public function tampilkanLaporanGaji()
    {
        // Mengambil data gaji untuk tampilan awal
        $users = User::with(['profil', 'gaji.slipGaji'])
            ->where('jabatan', 'pegawai') // Hanya pegawai
            ->get();

        // Pastikan Anda mengirimkan 'usersJson' ke view seperti ini
        return view('Pimpinan.laporangaji', [
            'usersJson' => $users->toJson() 
        ]);
    }


    /**
     * Mengambil dan mengolah data rekap kehadiran bulanan dalam format JSON.
     */
    public function getRekapKehadiran(Request $request)
    {
        $request->validate(['periode' => 'required|date_format:Y-m']);

        $periode = Carbon::createFromFormat('Y-m', $request->periode);
        $bulan = $periode->month;
        $tahun = $periode->year;

        // 1. Menghitung jumlah hari kerja (asumsi Senin-Sabtu)
        $jumlahHariKerja = $periode->copy()->startOfMonth()
            ->diffInDaysFiltered(fn(Carbon $date) => !$date->isSunday(), $periode->copy()->endOfMonth()) + 1;

        // 2. Menghitung jumlah hari hadir per pegawai
        $hadirQuery = Presensi::select('id_user', DB::raw('COUNT(DISTINCT DATE(created_at)) as total_hadir'))
            ->whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)
            ->whereIn('id_user', fn($q) => $q->select('id_user')->from('users')->where('jabatan', 'pegawai'))
            ->whereRaw('EXISTS (SELECT 1 FROM presensi p_in WHERE p_in.id_user = presensi.id_user AND DATE(p_in.created_at) = DATE(presensi.created_at) AND p_in.status = "masuk") AND EXISTS (SELECT 1 FROM presensi p_out WHERE p_out.id_user = presensi.id_user AND DATE(p_out.created_at) = DATE(presensi.created_at) AND p_out.status = "keluar")')
            ->groupBy('id_user')->pluck('total_hadir', 'id_user');

        // 3. Menghitung rekap izin, sakit, dan alpha
        $izinSakitQuery = IzinSakit::select('user_id', 'keterangan', DB::raw('count(*) as total'))
            ->whereYear('tanggal_izin', $tahun)->whereMonth('tanggal_izin', $bulan)
            ->groupBy('user_id', 'keterangan')->get()->groupBy('user_id');

        // 4. Menggabungkan semua data
        $pegawai = User::with('profil')->where('jabatan', 'pegawai')->get();
        $rekapData = [];

        foreach ($pegawai as $user) {
            $userIzinSakit = $izinSakitQuery->get($user->id_user, collect())->pluck('total', 'keterangan');
            $rekapData[] = [
                'nama' => $user->nama,
                'jabatan' => $user->profil->detail_pekerjaan ?? 'N/A',
                'jumlah_hari_kerja' => $jumlahHariKerja,
                'hadir' => $hadirQuery->get($user->id_user, 0),
                'izin' => $userIzinSakit->get('Izin', 0),
                'izin_dinas' => $userIzinSakit->get('Izin Dinas', 0),
                'sakit' => $userIzinSakit->get('Sakit', 0),
                'alpha' => $userIzinSakit->get('Alpha', 0),
            ];
        }

        return response()->json($rekapData);
    }

    // Metode 'tampilkanizinsakit' tidak perlu diubah.
    public function tampilkanizinsakit(Request $request)
    {
        $employees = User::with('profil')->where('jabatan', 'pegawai')->orderBy('nama', 'asc')->get();
        $is_search = $request->filled('user_id') || $request->filled('bulan') || $request->filled('tahun');
        $query = IzinSakit::with(['user.profil'])->latest();

        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('bulan')) $query->whereMonth('tanggal_izin', $request->bulan);
        if ($request->filled('tahun')) $query->whereYear('tanggal_izin', $request->tahun);

        $rekapData = $query->get()->groupBy('user_id');

        $title = "Rekapitulasi Izin / Sakit";
        if ($is_search) {
            $bulan = $request->input('bulan');
            $tahun = $request->input('tahun', date('Y'));
            $title = "Hasil Pencarian Rekap";
            if ($bulan) $title .= " Bulan " . Carbon::create()->month($bulan)->locale('id')->translatedFormat('F');
            if ($tahun) $title .= " Tahun " . $tahun;
        }
        return view('Pimpinan.dataizin', compact('employees', 'rekapData', 'is_search', 'title'));
    }
}