<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoGalleryController extends Controller
{
    /**
     * Menampilkan galeri foto presensi.
     */
    public function index()
    {
        $photoPaths = Storage::disk('public')->files('foto_presensi');

        rsort($photoPaths);

        return view('Admin.PantauPresensi', [
            'photos' => $photoPaths
        ]);
    }
}