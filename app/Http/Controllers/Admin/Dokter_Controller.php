<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleUser;
use Illuminate\Http\Request;
use App\Models\Dokter;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Dokter_Controller extends Controller
{

    // validation & helper
    protected function validate_dokter(Request $request, $iduser = null)
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
            'bidang_dokter' => ['required', 'string', 'min:3', 'max:100'],
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
            'bidang_dokter.required' => 'Bidang dokter wajib diisi.',
            'bidang_dokter.min' => 'Bidang dokter minimal 3 karakter.',
            'bidang_dokter.max' => 'Bidang dokter maksimal 100 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.min' => 'Alamat minimal 5 karakter.',
            'alamat.max' => 'Alamat maksimal 100 karakter.',
        ]);
    }


    protected function format_nama($value)
    {
        return trim(ucwords(strtolower($value)));
    }



    // method
    public function daftar_dokter()
    {
        $dokterlist = User::leftJoin('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->where('role_user.idrole', 2)
            ->where('role_user.status', 1)
            ->leftJoin('dokter', 'user.iduser', '=', 'dokter.iduser')
            ->whereNull('user.deleted_at')
            ->whereNull('dokter.deleted_at')
            ->select('user.*', 'dokter.iddokter', 'dokter.no_hp', 'dokter.jenis_kelamin', 'dokter.bidang_dokter', 'dokter.alamat')
            ->orderBy('user.nama')
            ->get();

        return view('Admin.Dokter.daftar-dokter', compact('dokterlist'));
    }


    public function store_dokter(Request $request) {
        $validated = $this->validate_dokter($request);
        $user = User::create([
            'nama' => $this->format_nama($validated['nama']),
            'email' => strtolower($validated['email']),
            'password' => Hash::make('123456'),
        ]);
        Dokter::create([
            'iduser' => $user->iduser,
            'no_hp' => $validated['no_hp'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'bidang_dokter' => $validated['bidang_dokter'],
            'alamat' => $validated['alamat'],
        ]);
        RoleUser::create([
            'iduser' => $user->iduser,
            'idrole' => 2,
        ]);
        return redirect()
            ->route('Admin.Dokter.daftar-dokter')
            ->with('success', 'Data dokter berhasil ditambahkan.');
    }


    public function update_dokter(Request $request, $id) {
        $validated = $this->validate_dokter($request, $id);
        $dokter = Dokter::findOrFail($id);
        $user = User::findOrFail($dokter->iduser);
        $user->nama = $this->format_nama($validated['nama']);
        $user->email = strtolower($validated['email']);
        $user->save();
        $dokter->no_hp = $validated['no_hp'];
        $dokter->jenis_kelamin = $validated['jenis_kelamin'];
        $dokter->bidang_dokter = $validated['bidang_dokter'];
        $dokter->alamat = $validated['alamat'];
        $dokter->save();
        return redirect()
            ->route('Admin.Dokter.daftar-dokter')
            ->with('success', 'Data dokter berhasil diperbarui.');
    }


    public function delete_dokter($id) {
        $dokter = Dokter::findOrFail($id);
        $iduser = session('iduser');
        $dokter->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);
        User::where('iduser', $dokter->iduser)->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);
        return redirect()
            ->route('Admin.Dokter.daftar-dokter')
            ->with('success', 'Data dokter berhasil dihapus.');
    }

    public function save_dokter(Request $request, $iduser)
    {
        $validated = $this->validate_dokter($request, $iduser);
        $user = User::findOrFail($iduser);

        // Update user data
        $user->nama = $this->format_nama($validated['nama']);
        $user->email = strtolower($validated['email']);
        $user->save();

        // Create or update dokter record
        $dokter = Dokter::where('iduser', $iduser)->first();
        if (!$dokter) {
            Dokter::create([
                'iduser' => $iduser,
                'no_hp' => $validated['no_hp'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'bidang_dokter' => $validated['bidang_dokter'],
                'alamat' => $validated['alamat'],
            ]);
        } else {
            $dokter->no_hp = $validated['no_hp'];
            $dokter->jenis_kelamin = $validated['jenis_kelamin'];
            $dokter->bidang_dokter = $validated['bidang_dokter'];
            $dokter->alamat = $validated['alamat'];
            $dokter->save();
        }

        return redirect()
            ->route('Admin.Dokter.daftar-dokter')
            ->with('success', 'Data dokter berhasil disimpan.');
    }
}
//cihuy