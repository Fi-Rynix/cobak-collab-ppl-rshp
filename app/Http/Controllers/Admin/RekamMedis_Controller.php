<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RekamMedis;
use App\Models\DetailRekamMedis;
use App\Models\TemuDokter;
use App\Models\KodeTindakanTerapi;
use App\Models\RoleUser;

class RekamMedis_Controller extends Controller
{

    // validation & helper
    protected function validate_rekam_medis(Request $request)
    {
        return $request->validate([
            'idreservasi_dokter' => ['required', 'exists:temu_dokter,idreservasi_dokter'],
            'anamnesa' => ['required', 'string', 'max:1000'],
            'temuan_klinis' => ['required', 'string', 'max:1000'],
            'diagnosa' => ['required', 'string', 'max:1000'],
            'detail' => ['required', 'string', 'max:1000'],
            'idkode_tindakan_terapi' => ['required', 'exists:kode_tindakan_terapi,idkode_tindakan_terapi'],
        ], [
            'idreservasi_dokter.required' => 'Reservasi dokter wajib dipilih.',
            'idreservasi_dokter.exists' => 'Reservasi dokter tidak valid.',
            'anamnesa.required' => 'Anamnesa wajib diisi.',
            'anamnesa.max' => 'Anamnesa maksimal 1000 karakter.',
            'temuan_klinis.required' => 'Temuan klinis wajib diisi.',
            'temuan_klinis.max' => 'Temuan klinis maksimal 1000 karakter.',
            'diagnosa.required' => 'Diagnosa wajib diisi.',
            'diagnosa.max' => 'Diagnosa maksimal 1000 karakter.',
            'detail.required' => 'Detail wajib diisi.',
            'detail.max' => 'Detail maksimal 1000 karakter.',
            'idkode_tindakan_terapi.required' => 'Tindakan terapi wajib dipilih.',
            'idkode_tindakan_terapi.exists' => 'Tindakan terapi tidak valid.',
        ]);
    }

    protected function validate_update_rekam_medis(Request $request)
    {
        return $request->validate([
            'anamnesa' => ['required', 'string', 'max:1000'],
            'temuan_klinis' => ['required', 'string', 'max:1000'],
            'diagnosa' => ['required', 'string', 'max:1000'],
            'detail' => ['required', 'string', 'max:1000'],
            'idkode_tindakan_terapi' => ['required', 'exists:kode_tindakan_terapi,idkode_tindakan_terapi'],
        ], [
            'anamnesa.required' => 'Anamnesa wajib diisi.',
            'anamnesa.max' => 'Anamnesa maksimal 1000 karakter.',
            'temuan_klinis.required' => 'Temuan klinis wajib diisi.',
            'temuan_klinis.max' => 'Temuan klinis maksimal 1000 karakter.',
            'diagnosa.required' => 'Diagnosa wajib diisi.',
            'diagnosa.max' => 'Diagnosa maksimal 1000 karakter.',
            'detail.required' => 'Detail wajib diisi.',
            'detail.max' => 'Detail maksimal 1000 karakter.',
            'idkode_tindakan_terapi.required' => 'Tindakan terapi wajib dipilih.',
            'idkode_tindakan_terapi.exists' => 'Tindakan terapi tidak valid.',
        ]);
    }


    // method
    public function daftar_rekam_medis(Request $request)
    {
        $rekamMedislist = RekamMedis::whereNull('deleted_at')->with(['temuDokter.pet.pemilik.user', 'temuDokter.roleUser.user', 'detailRekamMedis.kodeTindakanTerapi'])
            ->orderBy('created_at', 'desc')
            ->get();

        $reservasilist = TemuDokter::whereNull('deleted_at')->with(['pet.pemilik.user', 'pet.rasHewan', 'roleUser.user'])
            ->where('status', 'W')
            ->get();

        $tindakanlist = KodeTindakanTerapi::whereNull('deleted_at')->get();

        $dokterlist = RoleUser::where('idrole', 2)
            ->where('status', 1)
            ->with('user')
            ->get();

        return view('Admin.RekamMedis.daftar-rekam-medis', compact('rekamMedislist', 'reservasilist', 'tindakanlist', 'dokterlist'));
    }

    public function store_rekam_medis(Request $request)
    {
        $validated = $this->validate_rekam_medis($request);

        $temuDokter = TemuDokter::findOrFail($validated['idreservasi_dokter']);

        $rekamMedis = RekamMedis::create([
            'idreservasi_dokter' => $validated['idreservasi_dokter'],
            'anamnesa' => $validated['anamnesa'],
            'temuan_klinis' => $validated['temuan_klinis'],
            'diagnosa' => $validated['diagnosa'],
            'dokter_pemeriksa' => $temuDokter->idrole_user,
        ]);

        DetailRekamMedis::create([
            'idrekam_medis' => $rekamMedis->idrekam_medis,
            'idkode_tindakan_terapi' => $validated['idkode_tindakan_terapi'],
            'detail' => $validated['detail'],
        ]);

        TemuDokter::where('idreservasi_dokter', $validated['idreservasi_dokter'])
            ->update(['status' => 'D']);

        return redirect()
            ->route('Admin.RekamMedis.daftar-rekam-medis')
            ->with('success', 'Rekam medis berhasil dibuat.');
    }

    public function update_rekam_medis(Request $request, $id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);
        $validated = $this->validate_update_rekam_medis($request);

        $rekamMedis->update([
            'anamnesa' => $validated['anamnesa'],
            'temuan_klinis' => $validated['temuan_klinis'],
            'diagnosa' => $validated['diagnosa'],
        ]);

        $detailRekamMedis = DetailRekamMedis::where('idrekam_medis', $rekamMedis->idrekam_medis)->first();
        if ($detailRekamMedis) {
            $detailRekamMedis->update([
                'idkode_tindakan_terapi' => $validated['idkode_tindakan_terapi'],
                'detail' => $validated['detail'],
            ]);
        }

        return redirect()
            ->route('Admin.RekamMedis.daftar-rekam-medis')
            ->with('success', 'Rekam medis berhasil diupdate.');
    }

    public function delete_rekam_medis($id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);
        $iduser = session('iduser');

        DetailRekamMedis::where('idrekam_medis', $rekamMedis->idrekam_medis)->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);

        TemuDokter::where('idreservasi_dokter', $rekamMedis->idreservasi_dokter)
            ->update(['status' => 'W']);

        $rekamMedis->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);

        return redirect()
            ->route('Admin.RekamMedis.daftar-rekam-medis')
            ->with('success', 'Rekam medis berhasil dihapus.');
    }
}

?>
