<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PemilikResepsionis_Controller extends Controller
{
    // validation & helper
    protected function validate_pemilik(Request $request, $iduser = null)
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
            'no_wa' => ['required', 'numeric', 'digits_between:10,15'],
            'alamat' => ['required', 'string', 'min:5'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.min' => 'Nama minimal 3 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'no_wa.required' => 'Nomor WA wajib diisi.',
            'no_wa.numeric' => 'Nomor WA harus berupa angka.',
            'no_wa.digits_between' => 'Nomor WA harus antara 10-15 digit.',
            'alamat.required' => 'Alamat wajib diisi.',
        ]);
    }

    protected function format_nama($value)
    {
        return trim(ucwords(strtolower($value)));
    }

    // method
    public function daftar_pemilik()
    {
        $pemiliklist = DB::table('user')
            ->leftJoin('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->leftJoin('pemilik', 'user.iduser', '=', 'pemilik.iduser')
            ->where('role_user.idrole', 5)
            ->whereNull('user.deleted_at')
            ->whereNull('pemilik.deleted_at')
            ->select('user.*', 'pemilik.idpemilik', 'pemilik.no_wa', 'pemilik.alamat')
            ->orderBy('user.nama')
            ->get();

        return view('Resepsionis.Pemilik.daftar-pemilik', compact('pemiliklist'));
    }

    public function store_pemilik(Request $request)
    {
        $validated = $this->validate_pemilik($request);
        
        $iduser = DB::table('user')->insertGetId([
            'nama' => $this->format_nama($validated['nama']),
            'email' => strtolower($validated['email']),
            'password' => Hash::make('123456'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pemilik')->insert([
            'iduser' => $iduser,
            'no_wa' => $validated['no_wa'],
            'alamat' => $validated['alamat'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('role_user')->insert([
            'iduser' => $iduser,
            'idrole' => 5,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('Resepsionis.Pemilik.daftar-pemilik')
            ->with('success', 'Data pemilik berhasil ditambahkan.');
    }

    public function save_pemilik(Request $request, $iduser)
    {
        $validated = $this->validate_pemilik($request, $iduser);

        DB::table('user')
            ->where('iduser', $iduser)
            ->update([
                'nama' => $this->format_nama($validated['nama']),
                'email' => strtolower($validated['email']),
                'updated_at' => now(),
            ]);

        $pemilikExists = DB::table('pemilik')
            ->where('iduser', $iduser)
            ->exists();

        if (!$pemilikExists) {
            DB::table('pemilik')->insert([
                'iduser' => $iduser,
                'no_wa' => $validated['no_wa'],
                'alamat' => $validated['alamat'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('pemilik')
                ->where('iduser', $iduser)
                ->update([
                    'no_wa' => $validated['no_wa'],
                    'alamat' => $validated['alamat'],
                    'updated_at' => now(),
                ]);
        }

        return redirect()
            ->route('Resepsionis.Pemilik.daftar-pemilik')
            ->with('success', 'Data pemilik berhasil disimpan.');
    }

    public function delete_pemilik($id)
    {
        $iduser = session('iduser');
        
        DB::table('pemilik')
            ->where('idpemilik', $id)
            ->update([
                'deleted_at' => now(),
                'deleted_by' => $iduser,
            ]);

        $pemilik = DB::table('pemilik')->where('idpemilik', $id)->first();
        
        DB::table('user')
            ->where('iduser', $pemilik->iduser)
            ->update([
                'deleted_at' => now(),
                'deleted_by' => $iduser,
            ]);

        return redirect()
            ->route('Resepsionis.Pemilik.daftar-pemilik')
            ->with('success', 'Data pemilik berhasil dihapus.');
    }
}
//cihuy
?>