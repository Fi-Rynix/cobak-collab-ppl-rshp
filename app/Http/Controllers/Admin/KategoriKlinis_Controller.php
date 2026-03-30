<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\KategoriKlinis;

class KategoriKlinis_Controller extends Controller
{
    // Validation & Helper
    protected function validate_kategori_klinis(Request $request, $id = null) {
        $uniqueRule = $id
            ? 'unique:kategori_klinis,nama_kategori_klinis,' . $id . ',idkategori_klinis'
            : 'unique:kategori_klinis,nama_kategori_klinis';

        return $request->validate([
            'nama_kategori_klinis' => ['required', 'string', 'max:50', $uniqueRule],
        ], [
            'nama_kategori_klinis.required' => 'Nama kategori wajib diisi.',
            'nama_kategori_klinis.unique'   => 'Nama kategori sudah ada.',
            'nama_kategori_klinis.max'     => 'Nama kategori maksimal 50 karakter.',
        ]);
    }

    protected function format_nama_kategori_klinis($nama) {
        return trim(ucwords(strtolower($nama)));
    }

    protected function generate_id_kategori_klinis() {
        $lastId = KategoriKlinis::max('idkategori_klinis') ?? 0;
        return $lastId + 1;
    }



    // Method
    public function daftar_kategori_klinis() {
        $kategori_klinislist = KategoriKlinis::whereNull('deleted_at')->get();
        return view('Admin.KategoriKlinis.daftar-kategori-klinis', compact('kategori_klinislist'));
    }

    public function store_kategori_klinis(Request $request) {
        $validated = $this->validate_kategori_klinis($request);
        KategoriKlinis::create([
            'idkategori_klinis'   => $this->generate_id_kategori_klinis(),
            'nama_kategori_klinis'=> $this->format_nama_kategori_klinis($validated['nama_kategori_klinis']),
        ]);
        return redirect()->route('Admin.KategoriKlinis.daftar-kategori-klinis')
            ->with('success', 'Kategori Klinis berhasil ditambahkan.');
    }

    public function update_kategori_klinis(Request $request, $id) {
        $validated = $this->validate_kategori_klinis($request, $id);
        $kategori_klinis = KategoriKlinis::findOrFail($id);
        $kategori_klinis->nama_kategori_klinis = $this->format_nama_kategori_klinis($validated['nama_kategori_klinis']);
        $kategori_klinis->save();
        return redirect()->route('Admin.KategoriKlinis.daftar-kategori-klinis')
            ->with('success', 'Kategori Klinis berhasil diperbarui.');
    }

    public function delete_kategori_klinis($id) {
        $kategori_klinis = KategoriKlinis::findOrFail($id);
        if ($kategori_klinis->kodeTindakanTerapi()->where('kode_tindakan_terapi.deleted_at', null)->exists()) {
            return redirect()->route('Admin.KategoriKlinis.daftar-kategori-klinis')
                ->with('error', 'Kategori Klinis ini memiliki record di tabel lain dan tidak dapat dihapus.');
        }
        $iduser = session('iduser');
        $kategori_klinis->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);
        return redirect()->route('Admin.KategoriKlinis.daftar-kategori-klinis')
            ->with('success', 'Kategori KLinis berhasil dihapus.');
    }
}

?>