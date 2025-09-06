<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Pegawai\DataQrController;

Route::get('/presensi/status/{uuid}/{user_id}', [DataQrController::class, 'check']);
