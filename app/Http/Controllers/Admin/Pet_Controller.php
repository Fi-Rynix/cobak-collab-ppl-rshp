<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\Pemilik;
use App\Models\RasHewan;

class Pet_Controller extends Controller
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

    protected function validate_update_pet(Request $request, $id)
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
    public function daftar_pet() {
        $petlist = Pet::whereNull('deleted_at')->with(['pemilik.user', 'rasHewan'])->get();
        $pemiliklist = Pemilik::whereNull('deleted_at')->with('user')->get();
        $rashevanlist = RasHewan::whereNull('deleted_at')->get();
        return view('Admin.Pet.daftar-pet', compact('petlist', 'pemiliklist', 'rashevanlist'));
    }

    public function store_pet(Request $request) {
        $validated = $this->validate_pet($request);
        Pet::create([
            'nama' => $validated['nama'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'warna_tanda' => $validated['warna_tanda'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'idpemilik' => $validated['idpemilik'],
            'idras_hewan' => $validated['idras_hewan'],
        ]);
        return redirect()
            ->route('Admin.Pet.daftar-pet')
            ->with('success', 'Data pet berhasil ditambahkan.');
    }

    public function update_pet(Request $request, $id) {
        $validated = $this->validate_update_pet($request, $id);
        $pet = Pet::findOrFail($id);
        $pet->nama = $validated['nama'];
        $pet->tanggal_lahir = $validated['tanggal_lahir'];
        $pet->warna_tanda = $validated['warna_tanda'];
        $pet->jenis_kelamin = $validated['jenis_kelamin'];
        $pet->idpemilik = $validated['idpemilik'];
        $pet->idras_hewan = $validated['idras_hewan'];
        $pet->save();
        return redirect()
            ->route('Admin.Pet.daftar-pet')
            ->with('success', 'Data pet berhasil diperbarui.');
    }

    public function delete_pet($id) {
        $pet = Pet::findOrFail($id);
        $iduser = session('iduser');
        $pet->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);
        return redirect()
            ->route('Admin.Pet.daftar-pet')
            ->with('success', 'Data pet berhasil dihapus.');
    }
}
//cihuy
?>