<?php

namespace App\Http\Controllers\Perawat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekamMedisPerawat_Controller extends Controller
{
    // validator & helper
    protected function validate_rekam_medis(Request $request)
    {
        return $request->validate([
            'idreservasi_dokter' => 'required|exists:temu_dokter,idreservasi_dokter',
            'anamnesa' => 'required|string|max:1000',
            'temuan_klinis' => 'required|string|max:1000',
            'diagnosa' => 'required|string|max:1000',
        ], [
            'idreservasi_dokter.required' => 'Reservasi dokter wajib dipilih.',
            'idreservasi_dokter.exists' => 'Reservasi dokter tidak valid.',
            'anamnesa.required' => 'Anamnesa wajib diisi.',
            'anamnesa.max' => 'Anamnesa maksimal 1000 karakter.',
            'temuan_klinis.required' => 'Temuan klinis wajib diisi.',
            'temuan_klinis.max' => 'Temuan klinis maksimal 1000 karakter.',
            'diagnosa.required' => 'Diagnosa wajib diisi.',
            'diagnosa.max' => 'Diagnosa maksimal 1000 karakter.',
        ]);
    }

    protected function validate_rekam_medis_update(Request $request)
    {
        return $request->validate([
            'anamnesa' => 'required|string|max:1000',
            'temuan_klinis' => 'required|string|max:1000',
            'diagnosa' => 'required|string|max:1000',
        ], [
            'anamnesa.required' => 'Anamnesa wajib diisi.',
            'anamnesa.max' => 'Anamnesa maksimal 1000 karakter.',
            'temuan_klinis.required' => 'Temuan klinis wajib diisi.',
            'temuan_klinis.max' => 'Temuan klinis maksimal 1000 karakter.',
            'diagnosa.required' => 'Diagnosa wajib diisi.',
            'diagnosa.max' => 'Diagnosa maksimal 1000 karakter.',
        ]);
    }


    // method
    public function daftar_rekam_medis()
    {
        $rekam_medis_list = DB::table('rekam_medis as rm')
            ->join('temu_dokter as td', 'rm.idreservasi_dokter', '=', 'td.idreservasi_dokter')
            ->join('pet as p', 'td.idpet', '=', 'p.idpet')
            ->join('pemilik as pm', 'p.idpemilik', '=', 'pm.idpemilik')
            ->join('user as u_pemilik', 'pm.iduser', '=', 'u_pemilik.iduser')
            ->select(
                'rm.idrekam_medis',
                'p.nama as pet_nama',
                'u_pemilik.nama as pemilik_nama',
                'rm.anamnesa',
                'rm.temuan_klinis',
                'rm.diagnosa',
                'rm.created_at',
                'rm.dokter_pemeriksa'
            )
            ->whereNull('rm.deleted_at')
            ->orderBy('rm.created_at', 'desc')
            ->get();

        foreach ($rekam_medis_list as $rekam) {
            $dokter = DB::table('role_user as ru')
                ->join('user as u', 'ru.iduser', '=', 'u.iduser')
                ->where('ru.idrole_user', '=', $rekam->dokter_pemeriksa)
                ->select('u.nama')
                ->first();
            
            $rekam->dokter_nama = $dokter ? $dokter->nama : '-';

            $detail_list = DB::table('detail_rekam_medis as drm')
                ->join('kode_tindakan_terapi as ktt', 'drm.idkode_tindakan_terapi', '=', 'ktt.idkode_tindakan_terapi')
                ->select('drm.detail', 'ktt.kode', 'ktt.deskripsi_tindakan_terapi')
                ->where('drm.idrekam_medis', '=', $rekam->idrekam_medis)
                ->whereNull('drm.deleted_at')
                ->get();
            
            $rekam->detail_list = $detail_list;
        }

        $reservasilist = DB::table('temu_dokter as td')
            ->leftJoin('rekam_medis as rm', 'td.idreservasi_dokter', '=', 'rm.idreservasi_dokter')
            ->join('pet as p', 'td.idpet', '=', 'p.idpet')
            ->join('pemilik as pm', 'p.idpemilik', '=', 'pm.idpemilik')
            ->join('user as u_pemilik', 'pm.iduser', '=', 'u_pemilik.iduser')
            ->join('ras_hewan as rh', 'p.idras_hewan', '=', 'rh.idras_hewan')
            ->select(
                'td.idreservasi_dokter',
                'td.no_urut',
                'td.waktu_daftar',
                'p.nama as pet_nama',
                'rh.nama_ras',
                'u_pemilik.nama as pemilik_nama',
                'td.idrole_user'
            )
            ->where('td.status', 'W')
            ->whereNull('td.deleted_at')
            ->whereNull('rm.idrekam_medis')
            ->get();

        foreach ($reservasilist as $reservasi) {
            $dokter = DB::table('role_user as ru')
                ->join('user as u', 'ru.iduser', '=', 'u.iduser')
                ->where('ru.idrole_user', '=', $reservasi->idrole_user)
                ->select('u.nama')
                ->first();
            
            $reservasi->dokter_nama = $dokter ? $dokter->nama : '-';
        }

        return view('Perawat.RekamMedis.daftar-rekam-medis', [
            'rekam_medis_list' => $rekam_medis_list,
            'reservasilist' => $reservasilist
        ]);
    }

    public function detail_rekam_medis($id)
    {
        $rekam_medis = DB::table('rekam_medis as rm')
            ->join('temu_dokter as td', 'rm.idreservasi_dokter', '=', 'td.idreservasi_dokter')
            ->join('pet as p', 'td.idpet', '=', 'p.idpet')
            ->join('pemilik as pm', 'p.idpemilik', '=', 'pm.idpemilik')
            ->join('user as u_pemilik', 'pm.iduser', '=', 'u_pemilik.iduser')
            ->join('ras_hewan as rh', 'p.idras_hewan', '=', 'rh.idras_hewan')
            ->join('jenis_hewan as jh', 'rh.idjenis_hewan', '=', 'jh.idjenis_hewan')
            ->select(
                'rm.idrekam_medis',
                'rm.anamnesa',
                'rm.temuan_klinis',
                'rm.diagnosa',
                'rm.created_at',
                'p.nama as nama_pet',
                'p.tanggal_lahir',
                'p.jenis_kelamin',
                'u_pemilik.nama as pemilik_nama',
                'rh.nama_ras',
                'jh.nama_jenis_hewan',
                'rm.dokter_pemeriksa'
            )
            ->where('rm.idrekam_medis', '=', $id)
            ->whereNull('rm.deleted_at')
            ->first();

        if (!$rekam_medis) {
            abort(404);
        }

        $dokter = DB::table('role_user as ru')
            ->join('user as u', 'ru.iduser', '=', 'u.iduser')
            ->where('ru.idrole_user', '=', $rekam_medis->dokter_pemeriksa)
            ->select('u.nama')
            ->first();
        
        $rekam_medis->dokter_nama = $dokter ? $dokter->nama : '-';

        $detail = DB::table('detail_rekam_medis as drm')
            ->join('kode_tindakan_terapi as ktt', 'drm.idkode_tindakan_terapi', '=', 'ktt.idkode_tindakan_terapi')
            ->select('drm.detail', 'ktt.kode', 'ktt.deskripsi_tindakan_terapi')
            ->where('drm.idrekam_medis', '=', $id)
            ->whereNull('drm.deleted_at')
            ->first();

        $rekam_medis->detail_list = $detail ? [$detail] : [];

        return view('Perawat.RekamMedis.detail-rekam-medis', ['rekam_medis' => $rekam_medis]);
    }

    public function store_rekam_medis(Request $request)
    {
        $validated = $this->validate_rekam_medis($request);

        $temu_dokter = DB::table('temu_dokter')
            ->where('idreservasi_dokter', $validated['idreservasi_dokter'])
            ->select('idrole_user')
            ->first();

        if (!$temu_dokter) {
            return redirect()->back()->with('error', 'Data reservasi tidak ditemukan.');
        }

        DB::table('rekam_medis')->insert([
            'idreservasi_dokter' => $validated['idreservasi_dokter'],
            'anamnesa' => $validated['anamnesa'],
            'temuan_klinis' => $validated['temuan_klinis'],
            'diagnosa' => $validated['diagnosa'],
            'dokter_pemeriksa' => $temu_dokter->idrole_user,
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('Perawat.RekamMedis.daftar-rekam-medis')
            ->with('success', 'Rekam medis berhasil ditambahkan');
    }

    public function update_rekam_medis($id, Request $request)
    {
        $validated = $this->validate_rekam_medis_update($request);

        DB::table('rekam_medis')
            ->where('idrekam_medis', '=', $id)
            ->update([
                'anamnesa' => $validated['anamnesa'],
                'temuan_klinis' => $validated['temuan_klinis'],
                'diagnosa' => $validated['diagnosa'],
            ]);

        return redirect()->route('Perawat.RekamMedis.daftar-rekam-medis')
            ->with('success', 'Rekam medis berhasil diperbarui');
    }

    public function delete_rekam_medis($id)
    {
        DB::table('rekam_medis')
            ->where('idrekam_medis', '=', $id)
            ->update([
                'deleted_at' => Carbon::now(),
                'deleted_by' => auth()->id(),
            ]);

        return redirect()->route('Perawat.RekamMedis.daftar-rekam-medis')
            ->with('success', 'Rekam medis berhasil dihapus');
    }
}
//cihuy