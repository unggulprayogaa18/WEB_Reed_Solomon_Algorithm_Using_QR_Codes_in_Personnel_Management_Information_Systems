<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\DataAkunAdminController;
use App\Http\Controllers\Admin\DataLaporanController;
use App\Http\Controllers\Admin\DataPegawaiController;
use App\Http\Controllers\Admin\DataPenggajianPegawaiController;
use App\Http\Controllers\Admin\DataPresensiQrController;
use App\Http\Controllers\Admin\IzinSakitController;
use App\Http\Controllers\Pegawai\DataQrController;
use App\Http\Controllers\Pegawai\RiwayatPrensesiPegawaiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DataAkunController;
use App\Http\Controllers\Pegawai\SlipGajiController as PegawaiSlipGajiController;
use App\Http\Controllers\PhotoGalleryController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Pemimpin\PemimpinController as PemimpinPemimpinController;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('logout', [LoginController::class, 'destroy'])->name('logout.get');

    Route::get('/dashboard-pemimpin', [DataAkunController::class, 'pemimpinDashboard'])->name('dashboard.pemimpin');
    Route::get('/dashboard-admin', [DataAkunController::class, 'adminDashboard'])->name('dashboard.admin');
    Route::get('/dashboard-pegawai', [DataAkunController::class, 'pegawaiDashboard'])->name('dashboard.pegawai');
});



Route::middleware(['auth', 'cek.jabatan:admin'])->group(function () {

    Route::get('/laporan/gaji', [DataLaporanController::class, 'tampilkanLaporanGaji'])->name('laporan.gaji');
    Route::get('/datapresensi', [DataPresensiQrController::class, 'index'])->name('data.presensi');
    Route::post('/activities', [DataPresensiQrController::class, 'store'])->name('activity.store');
    Route::put('/activities/{uuid}', [DataPresensiQrController::class, 'update'])->name('activity.update');
    Route::delete('/activities/{uuid}', [DataPresensiQrController::class, 'destroy'])->name('activity.destroy');
    Route::get('/activities/download/{uuid}/{format}/{size?}', [DataPresensiQrController::class, 'downloadQrCode'])->name('activity.downloadQr');
    Route::post('/activities/end-day', [ActivityController::class, 'endDayAttendance'])->name('activity.end_day');

    Route::get('/pegawai', [DataPegawaiController::class, 'index'])->name('pegawai.index');
    Route::post('/pegawai', [DataPegawaiController::class, 'store'])->name('pegawai.store');
    Route::get('/pegawai/{id_user}/edit', [DataPegawaiController::class, 'edit'])->name('pegawai.edit');
    Route::put('/pegawai/{id_user}', [DataPegawaiController::class, 'update'])->name('pegawai.update');
    Route::delete('/pegawai/{id_user}', [DataPegawaiController::class, 'destroy'])->name('pegawai.destroy');
    // ===== RUTE BARU UNTUK IZIN SAKIT =====
    Route::get('/izin-sakit/create', [IzinSakitController::class, 'create'])->name('izin.create');
    Route::post('/izin-sakit', [IzinSakitController::class, 'store'])->name('izin.store');
    Route::put('/admin/izin-sakit/{id}', [IzinSakitController::class, 'update'])->name('izin.update');
    Route::delete('/admin/izin-sakit/{id}', [IzinSakitController::class, 'destroy'])->name('izin.destroy');

    // ===== AKHIR RUTE BARU =====

    // --- RUTE AKUN YANG SUDAH DIPERBAIKI ---
    Route::get('/akun', [DataAkunAdminController::class, 'index'])->name('akun.index');
    Route::post('/akun', [DataAkunAdminController::class, 'store'])->name('akun.store');
    // Route::get('/akun/{id_user}/edit', [DataAkunAdminController::class, 'edit'])->name('akun.edit'); // Tidak diperlukan jika pakai modal/offcanvas
    Route::put('/akun/{id_user}', [DataAkunAdminController::class, 'update'])->name('akun.update');
    Route::delete('/akun/{id_user}', [DataAkunAdminController::class, 'destroy'])->name('akun.destroy');
    Route::get('/admin/laporan-gaji', [DataLaporanController::class, 'tampilkanLaporanGaji'])->name('admin.laporan.gaji');
    Route::get('/admin/penggajian', [DataPenggajianPegawaiController::class, 'index'])->name('penggajian.index');
    // GANTI DENGAN SATU BARIS INI
    Route::post('/penggajian/calculate', [DataPenggajianPegawaiController::class, 'calculateSalary'])->name('penggajian.calculate');

    Route::get('/admin/foto-presensi', [PhotoGalleryController::class, 'index'])->name('admin.foto.presensi');
    Route::get('/kirim-pengingat', [NotifikasiController::class, 'kirimPengingatKeluar']);
});

Route::middleware(['auth', 'cek.jabatan:pegawai'])->group(function () {
    Route::get('/presensiqrpegawai', [DataQrController::class, 'index'])->name('presensiqr.pegawai');
    Route::post('/pegawai-store', [DataQrController::class, 'storePresensi'])->name('presensi.store_scan');
    Route::get('/slipgajipegawai', [PegawaiSlipGajiController::class, 'index'])->name('slipgaji.pegawai');
    Route::get('/riwayatpresensi', [RiwayatPrensesiPegawaiController::class, 'index'])->name('presensi.history');
    Route::post('/validate-presence', [DataQrController::class, 'validatePresence'])->name('validate.presence');
});


Route::middleware(['auth', 'cek.jabatan:pemimpin'])->group(function () {
    Route::get('/laporan-gaji', [PemimpinPemimpinController::class, 'tampilkanLaporanGaji'])->name('laporan.gajibypemimpin');
    Route::get('/laporan/kehadiran', [PemimpinPemimpinController::class, 'exportLaporanKehadiran'])->name('laporan.kehadiran.export');
    Route::get('/data-izin-sakit', [PemimpinPemimpinController::class, 'tampilkanizinsakit'])->name('izin.buat');
    // Tambahkan baris ini di routes/web.php
    Route::get('/laporan/kehadiran/data', [PemimpinPemimpinController::class, 'getRekapKehadiran'])->name('laporan.kehadiran.data');

});


Route::get('/test-fonnte', function () {
    $token = '';
    $targetNumber = '';
    $response = Http::withHeaders(['Authorization' => $token])
        ->post('https://api.fonnte.com/send', [
            'target' => $targetNumber,
            'message' => 'lagi apa kak',
        ]);
    dd(json_decode($response->body(), true));
})->middleware('auth', 'cek.jabatan:admin');
