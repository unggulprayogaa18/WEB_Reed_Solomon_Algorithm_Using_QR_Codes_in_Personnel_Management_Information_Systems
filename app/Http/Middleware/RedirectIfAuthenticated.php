<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

class RedirectIfAuthenticated // Ini biasanya ada di vendor/laravel/framework/src/Illuminate/Auth/Middleware/RedirectIfAuthenticated.php
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards): Response|RedirectResponse
    {
        $guard = $guards[0] ?? null;

        if (Auth::guard($guard)->check()) {
            $user = Auth::user();

            // Arahkan ke dashboard sesuai jabatan
            return match ($user->jabatan) {
                'pemimpin' => redirect('/dashboard-pemimpin'),
                'admin' => redirect('/dashboard-admin'),
                'pegawai' => redirect('/dashboard-pegawai'),
                default => redirect('/dashboard'),
            };
        }

        return $next($request);
    }
}