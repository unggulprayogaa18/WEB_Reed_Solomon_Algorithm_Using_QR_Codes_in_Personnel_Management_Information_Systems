<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function create(): View
    {
        return view('auth.login'); 
    }

    /**
     * Menangani permintaan autentikasi.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            switch ($user->jabatan) {
                case 'pemimpin':
                    return redirect()->route('dashboard.pemimpin');
                case 'admin':
                    return redirect()->route('dashboard.admin');
                case 'pegawai':
                    return redirect()->route('dashboard.pegawai');
                default:
                    return redirect()->route('dashboard.pegawai'); 
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request): RedirectResponse
    {
       Log::info('Attempting logout for user: ' . (Auth::check() ? Auth::user()->email : 'Guest'));
       Auth::logout();
       Log::info('User logged out? ' . (Auth::check() ? 'No' : 'Yes'));

       $request->session()->invalidate();
       $request->session()->regenerateToken();

       Log::info('Redirecting to login after logout.');
       return redirect()->route('login');
    }
}