<?php



namespace App\Http\Controllers\Pegawai;



use App\Http\Controllers\Controller;

use App\Models\PegawaiActivity;

use App\Models\Presensi;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;



class DataQrController extends Controller

{

  public function index()

  {

    $user = Auth::user();

    $todayPresensiRecords = Presensi::where('id_user', $user->id_user)

      ->whereDate('created_at', Carbon::today())

      ->with('activity')

      ->orderBy('created_at', 'asc')

      ->get();

    return view('Pegawai.ScanQr.ScanQr', compact(

      'user',

      'todayPresensiRecords'

    ));

  }



  public function storePresensi(Request $request)

  {

    $request->validate([

      'uuid_aktivitas' => 'required|uuid|exists:pegawai_activities,uuid',

      'status' => 'required|in:masuk,keluar',

    ]);



    $user = Auth::user();

    $activity = PegawaiActivity::where('uuid', $request->uuid_aktivitas)->firstOrFail();

    $today = Carbon::today();



    if ($request->status === 'keluar') {

      $pernahMasukHariIni = Presensi::where('id_user', $user->id_user)

        ->where('id_activity', $activity->id_activity)

        ->where('status', 'masuk')

        ->whereDate('created_at', $today)

        ->exists();



      if (!$pernahMasukHariIni) {

        return response()->json(['message' => 'Anda harus presensi masuk setidaknya satu kali sebelum bisa keluar.'], 400);

      }

    }



    // Ambil data yang diperlukan dari session

    $path_foto = session('validated_photo_path');

    $latitude = session('latitude');

    $longitude = session('longitude');



    $presensi = Presensi::create([

      'id_user' => $user->id_user,

      'id_activity' => $activity->id_activity,

      'status' => $request->status,

      'foto_bukti' => $path_foto,

      'latitude' => $latitude,

      'longitude' => $longitude,

      // Kolom 'lokasi' sudah dihapus dari sini

    ]);



    return response()->json([

      'message' => 'Presensi ' . strtoupper($request->status) . ' berhasil!',

      'type' => 'success',

      'activity_name' => $activity->nama_aktivitas,

      'scan_time' => $presensi->created_at->format('d-m-Y H:i:s')

    ], 200);

  }



  public function check($uuid, $user_id)

  {

    $activity = PegawaiActivity::where('uuid', $uuid)->first();

    if (!$activity) {

      return response()->json(['message' => 'Aktivitas dengan QR Code ini tidak valid atau tidak ditemukan.'], 404);

    }

    $latestPresensi = Presensi::where('id_user', $user_id)

      ->where('id_activity', $activity->id_activity)

      ->whereDate('created_at', Carbon::today())

      ->orderBy('created_at', 'desc')

      ->first();

    $status = 'belum';

    if ($latestPresensi) {

      $status = $latestPresensi->status;

    }

    return response()->json([

      'status' => $status,

      'activity' => [

        'uuid' => $activity->uuid,

        'nama_aktivitas' => $activity->nama_aktivitas,

      ]

    ]);

  }



 public function validatePresence(Request $request)

  {

    // Asumsi validasi wajah berhasil

    $isFaceValid = true;



    if ($isFaceValid) {

      $user = Auth::user();

      $latitude = $request->input('latitude');

      $longitude = $request->input('longitude');

      $locationAddress = "Lokasi_Tidak_Ditemukan"; // Fallback jika API gagal



      // === LOGIKA REVERSE GEOCODING MENGGUNAKAN OPENSTREETMAP ===

      try {

        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&accept-language=id";

       

        // Penting: OpenStreetMap memerlukan User-Agent yang jelas

        $response = Http::withHeaders(['User-Agent' => 'AplikasiPresensi/1.0'])->get($url);



        if ($response->successful() && isset($response->json()['display_name'])) {

          $locationAddress = $response->json()['display_name'];

        }

      } catch (\Exception $e) {

        Log::error('Gagal melakukan Reverse Geocoding (Nominatim): ' . $e->getMessage());

      }

      // =========================================================



      // === LOGIKA NAMA FILE BARU (Tidak ada yang diubah) ===

      $userNameSlug = Str::slug($user->nama, '_');

      $locationSlug = Str::slug($locationAddress, '_');

     

      $imageName = 'presensi_' . $userNameSlug . '_' . now()->format('Y-m-d_H-i-s') . '_' . $locationSlug . '.jpeg';



      $imageData = $request->input('photo');

      $image = str_replace('data:image/jpeg;base64,', '', $imageData);

      $image = str_replace(' ', '+', $image);

      $decodedImage = base64_decode($image);



      $storagePath = base_path('../storage/foto_presensi');

      if (!File::exists($storagePath)) {

        File::makeDirectory($storagePath, 0775, true, true);

      }

      File::put($storagePath . '/' . $imageName, $decodedImage);

     

      $sessionPath = 'storage/foto_presensi/' . $imageName;



      // Simpan data ke session

      session([

        'validated_photo_path' => $sessionPath,

        'latitude' => $latitude,

        'longitude' => $longitude,

      ]);



      return response()->json([

        'status' => 'success',

        'message' => 'Foto Berhasil Ditangkap!',

      ]);

    }



    return response()->json([

      'status' => 'error',

      'message' => 'Foto Gagal Ditangkap. Silakan coba lagi.',

    ], 400);

  }

}