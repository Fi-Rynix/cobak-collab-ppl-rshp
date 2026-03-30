<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;

class Kategori_Controller extends Controller
{
    // Validation & Helper
    protected function validate_kategori(Request $request, $id = null) {
        $uniqueRule = $id
            ? 'unique:kategori,nama_kategori,' . $id . ',idkategori'
            : 'unique:kategori,nama_kategori';

        return $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', $uniqueRule],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique'   => 'Nama kategori sudah ada.',
            'nama_kategori.max'     => 'Nama kategori maksimal 100 karakter.',
        ]);
    }

    protected function format_nama_kategori($nama) {
        return trim(ucwords(strtolower($nama)));
    }

    protected function generate_id_kategori() {
        $lastId = Kategori::max('idkategori') ?? 0;
        return $lastId + 1;
    }



    // Method
    public function daftar_kategori() {
        $kategorilist = Kategori::whereNull('deleted_at')->get();
        return view('Admin.Kategori.daftar-kategori', compact('kategorilist'));
    }

    public function store_kategori(Request $request) {
        $validated = $this->validate_kategori($request);
        Kategori::create([
            'idkategori'   => $this->generate_id_kategori(),
            'nama_kategori'=> $this->format_nama_kategori($validated['nama_kategori']),
        ]);
        return redirect()->route('Admin.Kategori.daftar-kategori')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update_kategori(Request $request, $id) {
        $validated = $this->validate_kategori($request, $id);
        $kategori = Kategori::findOrFail($id);
        $kategori->nama_kategori = $this->format_nama_kategori($validated['nama_kategori']);
        $kategori->save();
        return redirect()->route('Admin.Kategori.daftar-kategori')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete_kategori($id) {
        $kategori = Kategori::findOrFail($id);
        if ($kategori->kodeTindakanTerapi()->where('kode_tindakan_terapi.deleted_at', null)->exists()) {
            return redirect()->route('Admin.Kategori.daftar-kategori')
                ->with('error', 'Kategori ini memiliki record di tabel lain dan tidak dapat dihapus.');
        }
        $iduser = session('iduser');
        $kategori->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);
        return redirect()->route('Admin.Kategori.daftar-kategori')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}//cihuy