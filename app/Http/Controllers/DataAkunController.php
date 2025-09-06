<?php

namespace App\Http\Controllers; // Atau sesuaikan namespace jika Anda menempatkannya di subfolder

use App\Models\PegawaiActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataAkunController extends Controller // Pertimbangkan untuk mengganti nama menjadi DashboardController
{
    /**
     * Tampilkan dashboard umum (jika diakses).
     */
    public function index()
    {
        $user = Auth::user();
        return view('dashboard', compact('user')); // resources/views/dashboard.blade.php
    }

    /**
     * Tampilkan dashboard admin.
     */ public function adminDashboard()
{
    $user = Auth::user();

    $totalPegawai = User::count();
    $totalAdmin = User::where('jabatan', 'admin')->count();
    $pegawai = User::where('jabatan', 'pegawai')->count();
    $pemimpin = User::where('jabatan', 'pemimpin')->count();

    // Mengambil total semua aktivitas tanpa filter tanggal
    $totalAktivitas = PegawaiActivity::count(); 

    return view('Admin.Dashboard', compact(
        'user', 
        'totalPegawai', 
        'totalAdmin',
        'pegawai',
        'pemimpin',
        'totalAktivitas' // Mengirim variabel baru ke view
    ));
}

    /**
     * Tampilkan dashboard pemimpin.
     */
    public function pemimpinDashboard()
    {

        $user = Auth::user();

       
        $totalPegawai = User::where('jabatan', '!=', 'admin')->count(); 
        $totalAdmin = User::where('jabatan', 'admin')->count();       
        $totalAktivitas = PegawaiActivity::count();               

        return view('pimpinan.dashboard', [
            'user' => $user,
            'totalPegawai' => $totalPegawai,
            'totalAdmin' => $totalAdmin,
            'totalAktivitas' => $totalAktivitas,
        ]);
    }

    /**
     * Tampilkan dashboard pegawai.
     */
    public function pegawaiDashboard()
    {
        $user = Auth::user(); 
        return view('Pegawai.Dashboard', compact('user')); 
    }
}