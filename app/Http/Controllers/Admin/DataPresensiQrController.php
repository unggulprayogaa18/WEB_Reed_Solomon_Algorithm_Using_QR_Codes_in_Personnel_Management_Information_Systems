<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PegawaiActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataPresensiQrController extends Controller
{
    /**
     * Menampilkan daftar aktivitas pegawai.
     */
    public function index()
    {
        // Mengambil data dengan paginasi, diurutkan dari yang terbaru
        $activities = PegawaiActivity::latest()->paginate(10);
        return view('Admin.DataPresensiWithQR', compact('activities'));
    }

    /**
     * Menyimpan aktivitas baru.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama_aktivitas' => 'required|string|max:255',
                'deskripsi' => 'required|string',
            ]);

            $defaultTime = '1970-01-01 00:00:00';
            $validatedData['jam_mulai'] = $defaultTime;
            $validatedData['jam_selesai'] = $defaultTime;


            $activity = PegawaiActivity::create($validatedData);

            $qrCodeContent = json_encode(['uuid' => $activity->uuid]);
            $qrCodeStoragePath = public_path('qrcodes');
            if (!File::exists($qrCodeStoragePath)) {
                File::makeDirectory($qrCodeStoragePath, 0777, true, true);
            }
            $qrCodeFileName = 'qrcodes/' . $activity->uuid . '.svg';
            QrCode::size(200)->format('svg')->errorCorrection('H')->generate($qrCodeContent, public_path($qrCodeFileName));
            $activity->update(['qrcode_path' => $qrCodeFileName]);

            return redirect()->back()->with('success', 'Aktivitas baru berhasil ditambahkan!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan aktivitas: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Memperbarui data aktivitas yang ada.
     */
    public function update(Request $request, $uuid)
    {
        try {
            $validatedData = $request->validate([
                'nama_aktivitas' => 'required|string|max:255',
                'deskripsi' => 'required|string',
            ]);

            $activity = PegawaiActivity::where('uuid', $uuid)->firstOrFail();
            
            $defaultTime = '1970-01-01 00:00:00';
            $validatedData['jam_mulai'] = $defaultTime;
            $validatedData['jam_selesai'] = $defaultTime;


            $activity->update($validatedData);

            return redirect()->back()->with('success', 'Aktivitas berhasil diperbarui!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui aktivitas: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus aktivitas dan QR code-nya.
     */
    public function destroy($uuid)
    {
        try {
            $activity = PegawaiActivity::where('uuid', $uuid)->firstOrFail();

            if ($activity->qrcode_path && File::exists(public_path($activity->qrcode_path))) {
                File::delete(public_path($activity->qrcode_path));
            }

            $activity->delete();

            return redirect()->back()->with('success', 'Aktivitas berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}