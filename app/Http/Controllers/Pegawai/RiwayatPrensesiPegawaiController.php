<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RiwayatPrensesiPegawaiController extends Controller
{
    /**
     * Menampilkan halaman riwayat presensi untuk pegawai yang sedang login.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            // Jika karena suatu hal user tidak terautentikasi, kembalikan ke halaman login.
            return redirect()->route('login');
        }

        // Ambil SEMUA data presensi (masuk dan keluar) untuk pengguna ini.
        // Gunakan with('activity') untuk efisiensi query (menghindari N+1 problem).
        // Urutkan berdasarkan waktu pembuatan dari yang paling BARU ke yang paling LAMA.
        // Gunakan paginate() untuk membagi data menjadi beberapa halaman agar tidak berat.
        $riwayatPresensi = Presensi::with('activity')
            ->where('id_user', $user->id_user)
            ->orderBy('created_at', 'desc')
            ->paginate(15); // Anda bisa mengubah angka 15 sesuai kebutuhan.

        // Kirim data yang sudah dipaginasi ke view.
        return view('Pegawai.RiwayatPresensi', compact('riwayatPresensi'));
    }
}