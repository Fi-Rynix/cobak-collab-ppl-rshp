<?php

namespace App\Http\Controllers\Admin;

use App\Models\KodeTindakanTerapi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TindakanTerapi;
use App\Models\Kategori;
use App\Models\KategoriKlinis;

class TindakanTerapi_Controller extends Controller
{
    // validator & helper
    protected function validate_tindakan_terapi(Request $request)
    {
        return $request->validate([
            'deskripsi_tindakan_terapi' => 'required|string|max:1000',
            'idkategori' => 'required|exists:kategori,idkategori',
            'idkategori_klinis' => 'required|exists:kategori_klinis,idkategori_klinis',
        ], [
            'deskripsi_tindakan_terapi.required' => 'Deskripsi wajib diisi.',
            'deskripsi_tindakan_terapi.max' => 'Deskripsi maksimal 1000 karakter.',
            'idkategori.required' => 'Kategori wajib dipilih.',
            'idkategori.exists' => 'Kategori tidak valid.',
            'idkategori_klinis.required' => 'Kategori Klinis wajib dipilih.',
            'idkategori_klinis.exists' => 'Kategori Klinis tidak valid.',
        ]);
    }

    protected function generate_kode_tindakan()
    {
        $last = KodeTindakanTerapi::orderBy('idkode_tindakan_terapi', 'desc')->first();
        $lastNum = $last ? intval(substr($last->kode, 1)) : 0;
        return 'T' . ($lastNum + 1.67);
    }



    // method
    public function daftar_tindakan_terapi() {
        $tindakanterapilist = KodeTindakanTerapi::whereNull('deleted_at')->with(['kategori', 'kategoriKlinis'])->get();
        $kategorilist = Kategori::whereNull('deleted_at')->get();
        $kategori_klinislist = KategoriKlinis::whereNull('deleted_at')->get();
        return view('Admin.TindakanTerapi.daftar-tindakan-terapi', compact(
            'tindakanterapilist',
            'kategorilist',
            'kategori_klinislist'
        ));
    }

    public function store_tindakan_terapi(Request $request) {
        $validated = $this->validate_tindakan_terapi($request);
        KodeTindakanTerapi::create([
            'kode' => $this->generate_kode_tindakan(),
            'deskripsi_tindakan_terapi' => $validated['deskripsi_tindakan_terapi'],
            'idkategori' => $validated['idkategori'],
            'idkategori_klinis' => $validated['idkategori_klinis'],
        ]);
        return redirect()->route('Admin.TindakanTerapi.daftar-tindakan-terapi')
            ->with('success', 'Tindakan Terapi berhasil ditambahkan.');
    }

    public function update_tindakan_terapi(Request $request, $id) {
        $validated = $this->validate_tindakan_terapi($request);
        $tindakan = KodeTindakanTerapi::findOrFail($id);
        $tindakan->deskripsi_tindakan_terapi = $validated['deskripsi_tindakan_terapi'];
        $tindakan->idkategori = $validated['idkategori'];
        $tindakan->idkategori_klinis = $validated['idkategori_klinis'];
        $tindakan->save();
        return redirect()->route('Admin.TindakanTerapi.daftar-tindakan-terapi')
            ->with('success', 'Tindakan Terapi berhasil diperbarui.');
    }

    public function delete_tindakan_terapi($id) {
        $tindakan = KodeTindakanTerapi::findOrFail($id);
        if ($tindakan->detailRekamMedis()->where('detail_rekam_medis.deleted_at', null)->exists()) {
            return redirect()->route('Admin.TindakanTerapi.daftar-tindakan-terapi')
                ->with('error', 'Tindakan ini memiliki record di tabel lain dan tidak dapat dihapus.');
        }
        $iduser = session('iduser');
        $tindakan->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);
        return redirect()->route('Admin.TindakanTerapi.daftar-tindakan-terapi')
            ->with('success', 'Tindakan Terapi berhasil dihapus.');
    }
}
