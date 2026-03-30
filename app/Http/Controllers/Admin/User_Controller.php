<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
//cihuy
class User_Controller extends Controller
{


    // validation & helper
    protected function validate_user(Request $request, $id = null) {
        return $request->validate([
            'nama' => ['required', 'string', 'max:2', 'min:1'],
            'email' => [
                'required',
                'email',
            ],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.min' => 'Nama minimal 3 karakter.',
            'nama.max' => 'Nama maksimal 500 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);
    }

    protected function format_nama($nama) {
        return trim(ucwords(strtolower($nama)));
    }

    protected function random_password($length = 6) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }


    
    // method
    public function daftar_user() {
        $userlist = User::whereNull('deleted_at')->get();
        return view('Admin.User.daftar-user', compact('userlist'));
    }

    public function store_user(Request $request) {
        $validated = $this->validate_user($request);
        User::create([
            'nama' => $this->format_nama($validated['nama']),
            'email' => $validated['email'],
            'password' => Hash::make('123456'),
        ]);
        return redirect()->route('Admin.User.daftar-user')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function update_user(Request $request, $id) {
        $validated = $this->validate_user($request, $id);
        $user = User::findOrFail($id);
        $user->nama = $this->format_nama($validated['nama']);
        $user->email = $validated['email'];
        $user->save();
        return redirect()->route('Admin.User.daftar-user')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function delete_user($id) {
        $user = User::findOrFail($id);
        if ($user->Pemilik || $user->Dokter || $user->Perawat) {
            return redirect()->route('Admin.User.daftar-user')
                ->with('error', 'User ini memiliki record di tabel lain dan tidak dapat dihapus.');
        }
        $iduser = session('iduser');
        $user->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);
        return redirect()->route('Admin.User.daftar-user')
            ->with('success', 'User berhasil dihapus.');
    }


    public function reset_password($id) {
        $user = User::findOrFail($id);
        $user->password = Hash::make('123456');
        $user->save();
        return redirect()->route('Admin.User.daftar-user')
            ->with('success', 'Password berhasil di-reset ke 123456.');
    }

    public function random_password_update($id) {
        $user = User::findOrFail($id);
        $randomPass = $this->random_password(6);
        $user->password = Hash::make($randomPass);
        $user->save();
        return redirect()
            ->route('Admin.User.daftar-user')
            ->with('success', "Password baru: $randomPass");
    }
}

?>
