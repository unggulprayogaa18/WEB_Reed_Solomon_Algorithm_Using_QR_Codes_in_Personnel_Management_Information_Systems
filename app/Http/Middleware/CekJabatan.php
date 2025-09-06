<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekJabatan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$jabatans // Menggunakan "spread operator" untuk menerima satu atau lebih jabatan
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$jabatans): Response
    {
        // 1. Pastikan pengguna sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Dapatkan data pengguna yang sedang login
        $user = Auth::user();

        // 3. Periksa apakah jabatan pengguna ada di dalam daftar jabatan yang diizinkan
        //    $jabatans adalah array dari parameter yang kita kirim dari route, misal: ['admin', 'pemimpin']
        if (in_array($user->jabatan, $jabatans)) {
            // Jika jabatan sesuai, izinkan akses ke halaman berikutnya.
            return $next($request);
        }

        // 4. Jika jabatan tidak sesuai, redirect pengguna ke dashboard mereka masing-masing
        //    Ini mencegah mereka mengakses halaman yang bukan haknya.
        switch ($user->jabatan) {
            case 'pemimpin':
                return redirect()->route('dashboard.pemimpin')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
            case 'admin':
                return redirect()->route('dashboard.admin')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
            case 'pegawai':
                return redirect()->route('dashboard.pegawai')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
            default:
                // Fallback jika terjadi kasus aneh, logout dan redirect ke login
                Auth::logout();
                return redirect()->route('login')->with('error', 'Jabatan tidak dikenali. Silakan login kembali.');
        }
    }
}
