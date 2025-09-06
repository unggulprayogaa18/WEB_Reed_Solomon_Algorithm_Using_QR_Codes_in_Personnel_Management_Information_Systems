<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\IzinSakit;
use App\Models\PegawaiActivity;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IzinSakitController extends Controller
{
    /**
     * Display a form for creating a new resource OR show a recap of existing records.
     */
     public function create(Request $request)
    {
        $employees = User::with('profil')->where('jabatan', 'pegawai')->orderBy('nama', 'asc')->get();
        $activities = PegawaiActivity::orderBy('nama_aktivitas', 'asc')->get();
        $is_search = $request->filled('user_id') || $request->filled('bulan') || $request->filled('tahun');

        // --- START PERUBAHAN LOGIKA REKAP ---
        $query = IzinSakit::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_izin', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_izin', $request->tahun);
        }

        // Ambil semua data yang cocok, lalu kelompokkan berdasarkan user_id
        $izinSakits = $query->get();
        $rekapData = $izinSakits->groupBy('user_id');
        // --- END PERUBAHAN LOGIKA REKAP ---


        $title = "Rekapitulasi Izin / Sakit";
        if ($is_search) {
            $bulan = $request->input('bulan');
            $tahun = $request->input('tahun', date('Y'));
            $title = "Hasil Pencarian Rekap";
            if ($bulan) {
                $title .= " Bulan " . Carbon::create()->month($bulan)->translatedFormat('F');
            }
            if ($tahun) {
                $title .= " Tahun " . $tahun;
            }
        }
        
        // Pass data yang sudah dikelompokkan ke view
        return view('Admin.Izin_sakit', compact('employees', 'activities', 'rekapData', 'is_search', 'title'));
    }

    /**
     * Store a newly created sick/leave request in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'         => 'required|exists:users,id_user',
            'jenis_pembayaran'=> 'required|string',
            'tanggal_izin'    => 'required|date',
            'keterangan'      => 'required|string|max:1000',
            'buat_presensi'   => 'required|boolean',
            'jam_masuk'       => 'required_if:buat_presensi,1|nullable|date_format:H:i',
            'jam_keluar'      => 'required_if:buat_presensi,1|nullable|date_format:H:i|after:jam_masuk',
            'aktivitas' => [
                'required',
                'exists:pegawai_activities,id_activity',
                function ($attribute, $value, $fail) use ($request) {
                    $activity = PegawaiActivity::find($value);
                    if ($activity) {
                        $isDuplicate = IzinSakit::where('user_id', $request->user_id)
                            ->where('aktivitas', $activity->nama_aktivitas)
                            ->whereDate('tanggal_izin', $request->tanggal_izin)
                            ->exists();
                        if ($isDuplicate) {
                            $fail('Pegawai ini sudah memiliki izin untuk aktivitas yang sama pada tanggal tersebut.');
                        }
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $activity = PegawaiActivity::find($request->aktivitas);

            $izinSakit = IzinSakit::create([
                'user_id'          => $request->user_id,
                'detail_pekerjaan' => $request->detail_pekerjaan,
                'aktivitas'        => $activity->nama_aktivitas,
                'jenis_pembayaran' => $request->jenis_pembayaran,
                'tanggal_izin'     => $request->tanggal_izin,
                'keterangan'       => $request->keterangan,
                'status'           => 'disetujui',
                'jam_masuk'        => $request->buat_presensi ? $request->jam_masuk : null,
                'jam_keluar'       => $request->buat_presensi ? $request->jam_keluar : null,
            ]);

            if ($request->buat_presensi) {
                $waktuMasuk = Carbon::parse($request->tanggal_izin . ' ' . $request->jam_masuk);
                $waktuKeluar = Carbon::parse($request->tanggal_izin . ' ' . $request->jam_keluar);

                Presensi::create(['id_user' => $request->user_id, 'id_activity' => $request->aktivitas, 'izin_sakit_id' => $izinSakit->id, 'status' => 'masuk', 'created_at' => $waktuMasuk, 'updated_at' => $waktuMasuk]);
                Presensi::create(['id_user' => $request->user_id, 'id_activity' => $request->aktivitas, 'izin_sakit_id' => $izinSakit->id, 'status' => 'keluar', 'created_at' => $waktuKeluar, 'updated_at' => $waktuKeluar]);
            }

            DB::commit();
            return redirect()->route('izin.create')->with('success', 'Data izin berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $izinSakit = IzinSakit::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tanggal_izin_edit'  => 'required|date',
            'keterangan_edit'    => 'required|string|max:1000',
            'status_edit'        => ['required', Rule::in(['diajukan', 'disetujui', 'ditolak'])],
            'buat_presensi_edit' => 'required|boolean',
            'jam_masuk_edit'     => 'required_if:buat_presensi_edit,1|nullable|date_format:H:i',
            'jam_keluar_edit'    => 'required_if:buat_presensi_edit,1|nullable|date_format:H:i|after:jam_masuk_edit',
            'aktivitas_edit' => [
                'required',
                'exists:pegawai_activities,id_activity',
                function ($attribute, $value, $fail) use ($request, $izinSakit) {
                    $activity = PegawaiActivity::find($value);
                    if ($activity) {
                        $isDuplicate = IzinSakit::where('user_id', $izinSakit->user_id)
                            ->where('aktivitas', $activity->nama_aktivitas)
                            ->whereDate('tanggal_izin', $request->tanggal_izin_edit)
                            ->where('id', '!=', $izinSakit->id)
                            ->exists();
                        if ($isDuplicate) {
                            $fail('Pegawai ini sudah terdaftar untuk aktivitas yang sama pada tanggal tersebut.');
                        }
                    }
                },
            ],
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('izin.create')->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $selectedActivity = PegawaiActivity::find($request->aktivitas_edit);

            // Hapus presensi lama yang terkait dengan izin ini
            Presensi::where('izin_sakit_id', $id)->delete();

            // Update data izin sakit
            $izinSakit->update([
                'aktivitas'    => $selectedActivity->nama_aktivitas,
                'tanggal_izin' => $request->tanggal_izin_edit,
                'keterangan'   => $request->keterangan_edit,
                'status'       => $request->status_edit,
                'jam_masuk'    => $request->buat_presensi_edit ? $request->jam_masuk_edit : null,
                'jam_keluar'   => $request->buat_presensi_edit ? $request->jam_keluar_edit : null,
            ]);

            // Jika presensi diaktifkan dan status disetujui, buat ulang data presensi
            if ($request->buat_presensi_edit && $request->status_edit === 'disetujui') {
                $waktuMasuk = Carbon::parse($request->tanggal_izin_edit . ' ' . $request->jam_masuk_edit);
                $waktuKeluar = Carbon::parse($request->tanggal_izin_edit . ' ' . $request->jam_keluar_edit);
                
                Presensi::create(['id_user' => $izinSakit->user_id, 'id_activity' => $selectedActivity->id_activity, 'izin_sakit_id' => $izinSakit->id, 'status' => 'masuk', 'created_at' => $waktuMasuk, 'updated_at' => $waktuMasuk]);
                Presensi::create(['id_user' => $izinSakit->user_id, 'id_activity' => $selectedActivity->id_activity, 'izin_sakit_id' => $izinSakit->id, 'status' => 'keluar', 'created_at' => $waktuKeluar, 'updated_at' => $waktuKeluar]);
            }

            DB::commit();
            return redirect()->route('izin.create')->with('success', 'Data pengajuan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('izin.create')->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $izinSakit = IzinSakit::findOrFail($id);

            // Hapus juga data presensi yang terkait
            Presensi::where('izin_sakit_id', $id)->delete();
            
            // Hapus data izin
            $izinSakit->delete();

            DB::commit();
            return redirect()->route('izin.create')->with('success', 'Data pengajuan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('izin.create')->with('error', 'Gagal menghapus data pengajuan: ' . $e->getMessage());
        }
    }
}
// <?php

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\User;
// use App\Models\IzinSakit;
// use App\Models\PegawaiActivity;
// use App\Models\Presensi;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\Rule;

// class IzinSakitController extends Controller
// {
//     /**
//      * Display the form for creating a new resource and show the history.
//      */
//     public function create()
//     {
//         $employees = User::with('profil')->where('jabatan', 'pegawai')->orderBy('nama', 'asc')->get();
//         $activities = PegawaiActivity::orderBy('nama_aktivitas', 'asc')->get();
//         $izinSakits = IzinSakit::with('user')->latest()->paginate(10);
//         return view('Admin.Izin_sakit', compact('employees', 'activities', 'izinSakits'));
//     }

//     /**
//      * Store a newly created sick/leave request in storage.
//      */
//     public function store(Request $request)
//     {
//         // 1. Validation with conditional rules for time inputs
//         $validator = Validator::make($request->all(), [
//             'user_id'        => 'required|exists:users,id_user',
//             'jenis_pembayaran' => 'required|string',
//             'tanggal_izin'   => 'required|date',
//             'keterangan'     => 'required|string|max:1000',
//             'buat_presensi'  => 'required|boolean', // Validate the toggle value
            
//             // Time fields are only required if 'buat_presensi' is true (1)
//             'jam_masuk'      => 'required_if:buat_presensi,1|nullable|date_format:H:i',
//             'jam_keluar'     => 'required_if:buat_presensi,1|nullable|date_format:H:i|after:jam_masuk',
            
//             'aktivitas' => [
//                 'required',
//                 'exists:pegawai_activities,id_activity',
//                 function ($attribute, $value, $fail) use ($request) {
//                     $activity = \App\Models\PegawaiActivity::find($value);
//                     if ($activity) {
//                         $isDuplicate = \App\Models\IzinSakit::where('user_id', $request->user_id)
//                             ->where('aktivitas', $activity->nama_aktivitas)
//                             ->whereDate('tanggal_izin', $request->tanggal_izin)
//                             ->exists();
//                         if ($isDuplicate) {
//                             $fail('Karyawan ini sudah terdaftar untuk aktivitas yang sama pada tanggal tersebut.');
//                         }
//                     }
//                 },
//             ],
//         ]);

//         if ($validator->fails()) {
//             return redirect()->back()->withErrors($validator)->withInput();
//         }

//         DB::beginTransaction();
//         try {
//             $activity = PegawaiActivity::find($request->aktivitas);

//             // 2. Create the IzinSakit record
//             $izinSakit = IzinSakit::create([
//                 'user_id'          => $request->user_id,
//                 'detail_pekerjaan' => $request->detail_pekerjaan,
//                 'aktivitas'        => $activity->nama_aktivitas,
//                 'jenis_pembayaran' => $request->jenis_pembayaran,
//                 'tanggal_izin'     => $request->tanggal_izin,
//                 'keterangan'       => $request->keterangan,
//                 'status'           => 'disetujui',
//                 // Save time only if presensi is active, otherwise save NULL
//                 'jam_masuk'        => $request->buat_presensi ? $request->jam_masuk : null,
//                 'jam_keluar'       => $request->buat_presensi ? $request->jam_keluar : null,
//             ]);

//             // 3. Conditionally create Presensi records
//             if ($request->buat_presensi) {
//                 $waktuMasuk = Carbon::parse($request->tanggal_izin . ' ' . $request->jam_masuk);
//                 $waktuKeluar = Carbon::parse($request->tanggal_izin . ' ' . $request->jam_keluar);

//                 Presensi::create(['id_user' => $request->user_id, 'id_activity' => $request->aktivitas, 'izin_sakit_id' => $izinSakit->id, 'status' => 'masuk', 'created_at' => $waktuMasuk, 'updated_at' => $waktuMasuk]);
//                 Presensi::create(['id_user' => $request->user_id, 'id_activity' => $request->aktivitas, 'izin_sakit_id' => $izinSakit->id, 'status' => 'keluar', 'created_at' => $waktuKeluar, 'updated_at' => $waktuKeluar]);
//             }

//             DB::commit();
//             return redirect()->route('izin.create')->with('success', 'Data izin berhasil disimpan.');

//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
//         }
//     }

//     public function update(Request $request, $id)
//     {
//         $izinSakit = IzinSakit::findOrFail($id);

//         $validator = Validator::make($request->all(), [
//             'tanggal_izin_edit'     => 'required|date',
//             'keterangan_edit'       => 'required|string|max:1000',
//             'status_edit'           => ['required', Rule::in(['diajukan', 'disetujui', 'ditolak'])],
//             'buat_presensi_edit'    => 'required|boolean', // Validate the toggle value

//             'jam_masuk_edit'        => 'required_if:buat_presensi_edit,1|nullable|date_format:H:i',
//             'jam_keluar_edit'       => 'required_if:buat_presensi_edit,1|nullable|date_format:H:i|after:jam_masuk_edit',

//             'aktivitas_edit' => [
//                 'required',
//                 'exists:pegawai_activities,id_activity',
//                 function ($attribute, $value, $fail) use ($request, $izinSakit) {
//                     $activity = \App\Models\PegawaiActivity::find($value);
//                     if ($activity) {
//                         $isDuplicate = \App\Models\IzinSakit::where('user_id', $izinSakit->user_id)
//                             ->where('aktivitas', $activity->nama_aktivitas)
//                             ->whereDate('tanggal_izin', $request->tanggal_izin_edit)
//                             ->where('id', '!=', $izinSakit->id) // Abaikan data yang sedang diedit
//                             ->exists();
//                         if ($isDuplicate) {
//                             $fail('Karyawan ini sudah terdaftar untuk aktivitas yang sama pada tanggal tersebut.');
//                         }
//                     }
//                 },
//             ],
//         ]);
        
//         if ($validator->fails()) {
//             return redirect()->route('izin.create')->withErrors($validator)->withInput();
//         }

//         DB::beginTransaction();
//         try {
//             $selectedActivity = PegawaiActivity::find($request->aktivitas_edit);

//             // 1. Clean up old presensi records associated with this izin
//             Presensi::where('izin_sakit_id', $id)->delete();

//             // 2. Update the main IzinSakit record
//             $izinSakit->update([
//                 'aktivitas'      => $selectedActivity->nama_aktivitas,
//                 'tanggal_izin'   => $request->tanggal_izin_edit,
//                 'keterangan'     => $request->keterangan_edit,
//                 'status'         => $request->status_edit,
//                 // Update time only if presensi is active, otherwise set to NULL
//                 'jam_masuk'      => $request->buat_presensi_edit ? $request->jam_masuk_edit : null,
//                 'jam_keluar'     => $request->buat_presensi_edit ? $request->jam_keluar_edit : null,
//             ]);

//             // 3. Re-create presensi records ONLY if toggle is on AND status is 'disetujui'
//             if ($request->buat_presensi_edit && $request->status_edit === 'disetujui') {
//                 $waktuMasuk = Carbon::parse($request->tanggal_izin_edit . ' ' . $request->jam_masuk_edit);
//                 $waktuKeluar = Carbon::parse($request->tanggal_izin_edit . ' ' . $request->jam_keluar_edit);
                
//                 Presensi::create(['id_user' => $izinSakit->user_id, 'id_activity' => $selectedActivity->id_activity, 'izin_sakit_id' => $izinSakit->id, 'status' => 'masuk', 'created_at' => $waktuMasuk, 'updated_at' => $waktuMasuk]);
//                 Presensi::create(['id_user' => $izinSakit->user_id, 'id_activity' => $selectedActivity->id_activity, 'izin_sakit_id' => $izinSakit->id, 'status' => 'keluar', 'created_at' => $waktuKeluar, 'updated_at' => $waktuKeluar]);
//             }

//             DB::commit();
//             return redirect()->route('izin.create')->with('success', 'Data pengajuan berhasil diperbarui.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->route('izin.create')->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy($id)
//     {
//         DB::beginTransaction();
//         try {
//             $izinSakit = IzinSakit::findOrFail($id);
//             // Presensi will also be deleted because of the DB transaction and logic above, but good practice to be explicit.
//             Presensi::where('izin_sakit_id', $id)->delete();
//             $izinSakit->delete();
//             DB::commit();
//             return redirect()->route('izin.create')->with('success', 'Data pengajuan berhasil dihapus.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->route('izin.create')->with('error', 'Gagal menghapus data pengajuan: ' . $e->getMessage());
//         }
//     }
// }

