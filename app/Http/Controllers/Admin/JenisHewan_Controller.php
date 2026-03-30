<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\JenisHewan;

class JenisHewan_Controller extends Controller
{

    // validation & helper
    protected function validate_jenis_hewan(Request $request, $id = null) {
        $uniqueRule = $id
            ? 'unique:jenis_hewan,nama_jenis_hewan,' . $id . ',idjenis_hewan'
            : 'unique:jenis_hewan,nama_jenis_hewan';

        return $request->validate([
            'nama_jenis_hewan' => [
                'required',
                'string',
                'max:255',
                'min:3',
                $uniqueRule,
            ],
        ], [
            'nama_jenis_hewan.required' => 'Nama jenis hewan wajib diisi.',
            'nama_jenis_hewan.string' => 'Nama jenis hewan harus berupa teks.',
            'nama_jenis_hewan.max' => 'Nama jenis hewan maksimal 255 karakter.',
            'nama_jenis_hewan.min' => 'Nama jenis hewan minimal 3 karakter.',
            'nama_jenis_hewan.unique' => 'Nama jenis hewan sudah ada.',
        ]);
    }

    protected function format_nama_jenis_hewan($nama) {
        return trim(ucwords(strtolower($nama)));
    }



    // method
    public function daftar_jenis_hewan() {
        $hewanlist = JenisHewan::whereNull('deleted_at')->with('rasHewan')->get();
        return view('Admin.JenisHewan.daftar-jenis-hewan', compact('hewanlist'));
    }

    // public function create_jenis_hewan() {
    //     return view('Admin.JenisHewan.create-jenis-hewan');
    // }

    public function store_jenis_hewan(Request $request) {
        $validated = $this->validate_jenis_hewan($request);
        JenisHewan::create(['nama_jenis_hewan' => $this->format_nama_jenis_hewan($validated['nama_jenis_hewan']),]);
        return redirect()->route('Admin.JenisHewan.daftar-jenis-hewan')->with('success', 'Jenis hewan berhasil ditambahkan.');
    }

    // public function edit_jenis_hewan($id) {
    //     $hewan = JenisHewan::find($id);
    //     return view('Admin.JenisHewan.edit-jenis-hewan', compact('hewan'));
    // }

    public function update_jenis_hewan(Request $request, $id) {
        $this->validate_jenis_hewan($request, $id);
        $hewan = JenisHewan::find($id);
        $hewan->nama_jenis_hewan = $this->format_nama_jenis_hewan($request->input('nama_jenis_hewan'));
        $hewan->save();
        return redirect()->route('Admin.JenisHewan.daftar-jenis-hewan')->with('success', 'Jenis hewan berhasil diperbarui.');
    }

    public function delete_jenis_hewan($id) {
        $hewan = JenisHewan::find($id);
        if ($hewan->rasHewan()->where('ras_hewan.deleted_at', null)->exists()) {
            return redirect()->route('Admin.JenisHewan.daftar-jenis-hewan')
                ->with('error', 'Jenis hewan ini memiliki ras hewan terkait dan tidak dapat dihapus.');
        }
        $iduser = session('iduser');
        $hewan->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);
        return redirect()->route('Admin.JenisHewan.daftar-jenis-hewan')
            ->with('success', 'Jenis hewan berhasil dihapus.');
    }
}
//cihuy
?>
