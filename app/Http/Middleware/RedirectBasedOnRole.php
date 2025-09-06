<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Logika untuk mengarahkan pengguna ke dashboard yang benar
            switch ($user->jabatan) {
                case 'pemimpin':
                    if (!$request->routeIs('dashboard.pemimpin')) {
                        return redirect()->route('dashboard.pemimpin');
                    }
                    break;
                case 'admin':
                    if (!$request->routeIs('dashboard.admin')) {
                        return redirect()->route('dashboard.admin');
                    }
                    break;
                case 'pegawai':
                    if (!$request->routeIs('dashboard.pegawai')) {
                        return redirect()->route('dashboard.pegawai');
                    }
                    break;
                default:
                    // Fallback jika jabatan tidak dikenali atau rute 'dashboard' diakses
                    if (!$request->routeIs('dashboard')) {
                        return redirect()->route('dashboard'); // Arahkan ke dashboard umum jika ada
                    }
                    break;
            }
        }
        // Lanjutkan request jika tidak ada pengalihan atau user tidak diautentikasi
        return $next($request);
    }
}