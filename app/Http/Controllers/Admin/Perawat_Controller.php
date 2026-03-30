<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Perawat;
use App\Models\RoleUser;
use App\Models\Pet;
use Illuminate\Support\Facades\Hash;

class Perawat_Controller extends Controller
{
    // validation & helper
    protected function validate_perawat(Request $request, $iduser = null)
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255', 'min:3'],
            'email' => [
                'required',
                'email',
                'max:255',
                $iduser
                    ? 'unique:user,email,' . $iduser . ',iduser'
                    : 'unique:user,email'
            ],
            'no_hp' => ['required', 'numeric', 'digits_between:10,15'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'pendidikan' => ['required', 'string', 'min:3', 'max:100'],
            'alamat' => ['required', 'string', 'min:5', 'max:100'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.min' => 'Nama minimal 3 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.numeric' => 'Nomor HP harus berupa angka.',
            'no_hp.digits_between' => 'Nomor HP harus antara 10-15 digit.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
            'pendidikan.required' => 'Pendidikan wajib diisi.',
            'pendidikan.min' => 'Pendidikan minimal 3 karakter.',
            'pendidikan.max' => 'Pendidikan maksimal 100 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.min' => 'Alamat minimal 5 karakter.',
            'alamat.max' => 'Alamat maksimal 100 karakter.',
        ]);
    }

    protected function format_nama($value) {
        return trim(ucwords(strtolower($value)));
    }

    protected function validate_update_perawat(Request $request, $id) {
        $perawat = Perawat::findOrFail($id);

        return $request->validate([
            'nama' => ['required', 'string', 'max:255', 'min:3'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:user,email,' . $perawat->iduser . ',iduser'
            ],
            'no_hp' => ['required', 'numeric', 'digits_between:10,15'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'pendidikan' => ['required', 'string', 'min:3', 'max:100'],
            'alamat' => ['required', 'string', 'min:5', 'max:100'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.min' => 'Nama minimal 3 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.numeric' => 'Nomor HP harus berupa angka.',
            'no_hp.digits_between' => 'Nomor HP harus antara 10-15 digit.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
            'pendidikan.required' => 'Pendidikan wajib diisi.',
            'pendidikan.min' => 'Pendidikan minimal 3 karakter.',
            'pendidikan.max' => 'Pendidikan maksimal 100 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.min' => 'Alamat minimal 5 karakter.',
            'alamat.max' => 'Alamat maksimal 100 karakter.',
        ]);
    }



    // method
    public function daftar_perawat()
    {
        $perawatlist = User::leftJoin('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->where('role_user.idrole', 3)
            ->where('role_user.status', 1)
            ->leftJoin('perawat', 'user.iduser', '=', 'perawat.iduser')
            ->whereNull('user.deleted_at')
            ->whereNull('perawat.deleted_at')
            ->select('user.*', 'perawat.idperawat', 'perawat.no_hp', 'perawat.jenis_kelamin', 'perawat.pendidikan', 'perawat.alamat')
            ->orderBy('user.nama')
            ->get();

        return view('Admin.Perawat.daftar-perawat', compact('perawatlist'));
    }

    public function store_perawat(Request $request) {
        $validated = $this->validate_perawat($request);
        $user = User::create([
            'nama' => $this->format_nama($validated['nama']),
            'email' => strtolower($validated['email']),
            'password' => Hash::make('123456'),
        ]);
        Perawat::create([
            'iduser' => $user->iduser,
            'no_hp' => $validated['no_hp'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'pendidikan' => $validated['pendidikan'],
            'alamat' => $validated['alamat'],
        ]);
        RoleUser::create([
            'iduser' => $user->iduser,
            'idrole' => 3,
        ]);
        return redirect()
            ->route('Admin.Perawat.daftar-perawat')
            ->with('success', 'Data perawat berhasil ditambahkan.');
    }

    public function update_perawat(Request $request, $id) {
        $validated = $this->validate_update_perawat($request, $id);
        $perawat = Perawat::findOrFail($id);
        $user = User::findOrFail($perawat->iduser);
        $user->nama = $this->format_nama($validated['nama']);
        $user->email = strtolower($validated['email']);
        $user->save();
        $perawat->no_hp = $validated['no_hp'];
        $perawat->jenis_kelamin = $validated['jenis_kelamin'];
        $perawat->pendidikan = $validated['pendidikan'];
        $perawat->alamat = $validated['alamat'];
        $perawat->save();
        return redirect()
            ->route('Admin.Perawat.daftar-perawat')
            ->with('success', 'Data perawat berhasil diperbarui.');
    }

    public function delete_perawat($id) {
        $perawat = Perawat::findOrFail($id);
        $iduser = session('iduser');
        $perawat->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);
        User::where('iduser', $perawat->iduser)->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);
        return redirect()
            ->route('Admin.Perawat.daftar-perawat')
            ->with('success', 'Data perawat berhasil dihapus.');
    }

    public function save_perawat(Request $request, $iduser)
    {
        $validated = $this->validate_perawat($request, $iduser);
        $user = User::findOrFail($iduser);

        $user->nama = $this->format_nama($validated['nama']);
        $user->email = strtolower($validated['email']);
        $user->save();

        $perawat = Perawat::where('iduser', $iduser)->first();
        if (!$perawat) {
            Perawat::create([
                'iduser' => $iduser,
                'no_hp' => $validated['no_hp'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'pendidikan' => $validated['pendidikan'],
                'alamat' => $validated['alamat'],
            ]);
        } else {
            $perawat->no_hp = $validated['no_hp'];
            $perawat->jenis_kelamin = $validated['jenis_kelamin'];
            $perawat->pendidikan = $validated['pendidikan'];
            $perawat->alamat = $validated['alamat'];
            $perawat->save();
        }

        return redirect()
            ->route('Admin.Perawat.daftar-perawat')
            ->with('success', 'Data perawat berhasil disimpan.');
    }
}

?>