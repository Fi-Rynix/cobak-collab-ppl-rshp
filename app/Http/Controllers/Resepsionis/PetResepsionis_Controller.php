<?php

namespace App\Http\Controllers\Resepsionis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PetResepsionis_Controller extends Controller
{
    // validation & helper
    protected function validate_pet(Request $request)
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:100', 'min:3'],
            'tanggal_lahir' => ['required', 'date'],
            'warna_tanda' => ['required', 'string', 'max:45'],
            'jenis_kelamin' => ['required', 'in:J,B'],
            'idpemilik' => ['required', 'exists:pemilik,idpemilik'],
            'idras_hewan' => ['required', 'exists:ras_hewan,idras_hewan'],
        ], [
            'nama.required' => 'Nama pet wajib diisi.',
            'nama.min' => 'Nama pet minimal 3 karakter.',
            'nama.max' => 'Nama pet maksimal 100 karakter.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'warna_tanda.required' => 'Warna tanda wajib diisi.',
            'warna_tanda.max' => 'Warna tanda maksimal 45 karakter.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin harus J atau B.',
            'idpemilik.required' => 'Pemilik wajib dipilih.',
            'idpemilik.exists' => 'Pemilik tidak valid.',
            'idras_hewan.required' => 'Ras hewan wajib dipilih.',
            'idras_hewan.exists' => 'Ras hewan tidak valid.',
        ]);
    }

    // method
    public function daftar_pet()
    {
        $petlist = DB::table('pet')
            ->leftJoin('pemilik', 'pet.idpemilik', '=', 'pemilik.idpemilik')
            ->leftJoin('user', 'pemilik.iduser', '=', 'user.iduser')
            ->leftJoin('ras_hewan', 'pet.idras_hewan', '=', 'ras_hewan.idras_hewan')
            ->whereNull('pet.deleted_at')
            ->select('pet.*', 'user.nama as pemilik_nama', 'ras_hewan.nama_ras')
            ->orderBy('pet.nama')
            ->get();

        $pemiliklist = DB::table('pemilik')
            ->leftJoin('user', 'pemilik.iduser', '=', 'user.iduser')
            ->whereNull('pemilik.deleted_at')
            ->select('pemilik.idpemilik', 'user.nama')
            ->orderBy('user.nama')
            ->get();

        $rashevanlist = DB::table('ras_hewan')
            ->whereNull('deleted_at')
            ->orderBy('nama_ras')
            ->get();

        return view('Resepsionis.Pet.daftar-pet', compact('petlist', 'pemiliklist', 'rashevanlist'));
    }

    public function store_pet(Request $request)
    {
        $validated = $this->validate_pet($request);

        DB::table('pet')->insert([
            'nama' => $validated['nama'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'warna_tanda' => $validated['warna_tanda'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'idpemilik' => $validated['idpemilik'],
            'idras_hewan' => $validated['idras_hewan'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('Resepsionis.Pet.daftar-pet')
            ->with('success', 'Data pet berhasil ditambahkan.');
    }

    public function update_pet(Request $request, $id)
    {
        $validated = $this->validate_pet($request);

        DB::table('pet')
            ->where('idpet', $id)
            ->update([
                'nama' => $validated['nama'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'warna_tanda' => $validated['warna_tanda'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'idpemilik' => $validated['idpemilik'],
                'idras_hewan' => $validated['idras_hewan'],
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('Resepsionis.Pet.daftar-pet')
            ->with('success', 'Data pet berhasil diperbarui.');
    }

    public function delete_pet($id)
    {
        $iduser = session('iduser');

        DB::table('pet')
            ->where('idpet', $id)
            ->update([
                'deleted_at' => now(),
                'deleted_by' => $iduser,
            ]);

        return redirect()
            ->route('Resepsionis.Pet.daftar-pet')
            ->with('success', 'Data pet berhasil dihapus.');
    }
}
//cihuy
?>
