<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JenisHewan;
use App\Models\RasHewan;

class RasHewan_Controller extends Controller
{
    // validation & helper
    protected function validate_ras_hewan(Request $request, $id = null) {

        $uniqueRule = $id
            ? 'unique:ras_hewan,nama_ras,' . $id . ',idras_hewan,idjenis_hewan,' . $request->idjenis_hewan
            : 'unique:ras_hewan,nama_ras,NULL,idras_hewan,idjenis_hewan,' . $request->idjenis_hewan;

        return $request->validate([
            'idjenis_hewan' => 'required|integer|exists:jenis_hewan,idjenis_hewan',
            'nama_ras' => [
                'required',
                'string',
                'max:100',
                'min:2',
                $uniqueRule,
            ],
        ], [
            'idjenis_hewan.required' => 'Jenis hewan wajib dipilih.',
            'idjenis_hewan.exists' => 'Jenis hewan tidak valid.',
            'nama_ras.required' => 'Nama ras wajib diisi.',
            'nama_ras.string' => 'Nama ras harus berupa teks.',
            'nama_ras.max' => 'Nama ras maksimal 100 karakter.',
            'nama_ras.min' => 'Nama ras minimal 2 karakter.',
            'nama_ras.unique' => 'Nama ras sudah terdaftar untuk jenis hewan ini.',
        ]);
    }

    protected function format_nama_ras($nama)
    {
        return ucwords(strtolower(trim($nama)));
    }



    // method
    public function daftar_ras_hewan() {
        $hewanRasList = JenisHewan::whereNull('deleted_at')->with('rasHewan')->get();
        return view('Admin.RasHewan.daftar-ras-hewan', compact('hewanRasList'));
    }

    public function store_ras_hewan(Request $request) {
        $validated = $this->validate_ras_hewan($request);
        RasHewan::create([
            'idjenis_hewan' => $validated['idjenis_hewan'],
            'nama_ras'      => $this->format_nama_ras($validated['nama_ras']),
        ]);
        return redirect()->route('Admin.RasHewan.daftar-ras-hewan')
            ->with('success', 'Ras Hewan berhasil ditambahkan.');
    }

    public function update_ras_hewan(Request $request, $id) {
        $ras = RasHewan::findOrFail($id);
        $validated = $this->validate_ras_hewan($request, $id);
        $ras->update([
            'nama_ras' => $this->format_nama_ras($validated['nama_ras']),
        ]);
        return redirect()->route('Admin.RasHewan.daftar-ras-hewan')
            ->with('success', 'Ras Hewan berhasil diperbarui.');
    }

    public function delete_ras_hewan($id) {
        $ras = RasHewan::findOrFail($id);
        if ($ras->pets()->where('pet.deleted_at', null)->exists()) {
            return redirect()->route('Admin.RasHewan.daftar-ras-hewan')
                ->with('error', 'Ras ini digunakan pada data hewan dan tidak dapat dihapus.');
        }
        $iduser = session('iduser');
        $ras->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);
        return redirect()->route('Admin.RasHewan.daftar-ras-hewan')
            ->with('success', 'Ras Hewan berhasil dihapus.');
    }
}
