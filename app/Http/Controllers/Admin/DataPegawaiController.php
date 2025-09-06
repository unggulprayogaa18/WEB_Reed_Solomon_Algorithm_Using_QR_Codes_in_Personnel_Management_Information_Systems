<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DataPegawaiController extends Controller
{
    public function index()
    {
        $pegawai = User::where('jabatan', 'pegawai')->with('profil')->paginate(10);
        return view('Admin.DataPegawai', compact('pegawai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'jabatan' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string|max:255',
            'detail_pekerjaan' => 'required|string|max:255',
            'pekerjaan_lainnya' => 'required_if:detail_pekerjaan,Lainnya|nullable|string|max:255',
        ]);

        // Tentukan nilai detail pekerjaan yang akan disimpan
        $detailPekerjaanValue = $request->detail_pekerjaan;
        if ($request->detail_pekerjaan === 'Lainnya') {
            $detailPekerjaanValue = $request->pekerjaan_lainnya;
        }

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jabatan' => $request->jabatan,
            'no_telepon' => $request->no_telepon,
            'role' => 'pegawai',
            'email_verified_at' => now(),
        ]);

        Profil::create([
            'id_user' => $user->id_user,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'detail_pekerjaan' => $detailPekerjaanValue,
        ]);

        return redirect()->back()->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function update(Request $request, $id_user)
    {
        $user = User::where('id_user', $id_user)->firstOrFail();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id_user . ',id_user',
            'password' => 'nullable|string|min:6',
            'jabatan' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string|max:255',
            'detail_pekerjaan' => 'required|string|max:255',
            'pekerjaan_lainnya' => 'required_if:detail_pekerjaan,Lainnya|nullable|string|max:255',
        ]);
        
        // Tentukan nilai detail pekerjaan yang akan disimpan
        $detailPekerjaanValue = $request->detail_pekerjaan;
        if ($request->detail_pekerjaan === 'Lainnya') {
            $detailPekerjaanValue = $request->pekerjaan_lainnya;
        }

        DB::beginTransaction();
        try {
            $user->nama = $request->nama;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->jabatan = $request->jabatan;
            $user->no_telepon = $request->no_telepon;
            $user->save();

            $profil = Profil::firstOrNew(['id_user' => $user->id_user]);
            $profil->tanggal_lahir = $request->tanggal_lahir;
            $profil->alamat = $request->alamat;
            $profil->detail_pekerjaan = $detailPekerjaanValue;
            $profil->save();

            DB::commit();
            return redirect()->back()->with('success', 'Data pegawai berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal memperbarui pegawai: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data pegawai. Silakan coba lagi.');
        }
    }

    public function destroy($id_user)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id_user', $id_user)->firstOrFail();
            // Hapus profil terlebih dahulu jika tidak ada cascade delete di DB
            $user->profil()->delete();
            $user->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Data pegawai berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menghapus pegawai: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus data pegawai. Silakan coba lagi.');
        }
    }
}