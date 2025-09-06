<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DataAkunAdminController extends Controller
{
    /**
     * Menampilkan daftar semua akun.
     */
    public function index()
    {
        $auth = User::paginate(10);
        return view('Admin.DataAkun', compact('auth'));
    }

    /**
     * Menyimpan akun baru yang dibuat.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'jabatan' => ['required', 'string', 'in:admin,pegawai,pemimpin'], 
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jabatan' => $request->jabatan,
            'no_telepon' => $request->no_telepon,
        ]);

        return redirect()->route('akun.index')->with('success', 'Akun berhasil ditambahkan!');
    }

    /**
     * Memperbarui data akun yang sudah ada.
     */
    public function update(Request $request, User $id_user)
    {
        // 1. Validasi data utama (selain password)
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id_user->id_user . ',id_user'],
            'jabatan' => ['required', 'string', 'in:admin,pegawai,pemimpin'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        // 2. Update data utama pada user
        $id_user->nama = $request->nama;
        $id_user->email = $request->email;
        $id_user->jabatan = $request->jabatan;
        $id_user->no_telepon = $request->no_telepon;

        // 3. Cek apakah admin ingin mengubah password (hanya jika kolom diisi)
        if ($request->filled('password')) {
            // Jika diisi, validasi dan update password
            $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
            $id_user->password = Hash::make($request->password);
        }

        // 4. Simpan semua perubahan
        $id_user->save();

        // 5. Redirect kembali dengan pesan sukses tanpa mengganggu sesi admin
        return redirect()->route('akun.index')->with('success', 'Akun berhasil diperbarui!');
    }

    /**
     * Menghapus akun dari database.
     */
    public function destroy(User $id_user) 
    {
        try {
            $id_user->delete();
            return redirect()->route('akun.index')->with('success', 'Akun berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('akun.index')->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }
}