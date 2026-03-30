<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekamMedisDokter_Controller extends Controller
{
    // validator & helper
    protected function validate_detail_rekam_medis(Request $request)
    {
        return $request->validate([
            'idkode_tindakan_terapi' => 'required|exists:kode_tindakan_terapi,idkode_tindakan_terapi',
            'detail' => 'required|string|max:1000',
        ], [
            'idkode_tindakan_terapi.required' => 'Kode tindakan wajib dipilih.',
            'idkode_tindakan_terapi.exists' => 'Kode tindakan tidak valid.',
            'detail.required' => 'Detail tindakan wajib diisi.',
            'detail.max' => 'Detail tindakan maksimal 1000 karakter.',
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

            $detail = DB::table('detail_rekam_medis as drm')
                ->join('kode_tindakan_terapi as ktt', 'drm.idkode_tindakan_terapi', '=', 'ktt.idkode_tindakan_terapi')
                ->select('drm.detail', 'ktt.kode', 'ktt.deskripsi_tindakan_terapi')
                ->where('drm.idrekam_medis', '=', $rekam->idrekam_medis)
                ->whereNull('drm.deleted_at')
                ->first();
            
            $rekam->detail_list = $detail ? [$detail] : [];
        }

        return view('Dokter.RekamMedis.daftar-rekam-medis', [
            'rekam_medis_list' => $rekam_medis_list
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
            ->select('drm.detail', 'drm.iddetail_rekam_medis', 'ktt.kode', 'ktt.deskripsi_tindakan_terapi', 'drm.idkode_tindakan_terapi')
            ->where('drm.idrekam_medis', '=', $id)
            ->whereNull('drm.deleted_at')
            ->first();

        $rekam_medis->detail_list = $detail ? [$detail] : [];

        $kode_tindakan = DB::table('kode_tindakan_terapi')
            ->whereNull('deleted_at')
            ->get();

        $current_idrole_user = DB::table('role_user')
            ->where('iduser', '=', auth()->id())
            ->where('idrole', '=', 2)
            ->value('idrole_user');

        return view('Dokter.RekamMedis.detail-rekam-medis', [
            'rekam_medis' => $rekam_medis,
            'kode_tindakan' => $kode_tindakan,
            'current_idrole_user' => $current_idrole_user
        ]);
    }

    public function store_detail($idrekam_medis, Request $request)
    {
        $rekam_medis = DB::table('rekam_medis')
            ->where('idrekam_medis', $idrekam_medis)
            ->whereNull('deleted_at')
            ->first();

        if (!$rekam_medis) {
            return redirect()->back()->with('error', 'Rekam medis tidak ditemukan.');
        }

        $current_idrole_user = DB::table('role_user')
            ->where('iduser', '=', auth()->id())
            ->where('idrole', '=', 2)
            ->value('idrole_user');

        if (!$current_idrole_user || $current_idrole_user != $rekam_medis->dokter_pemeriksa) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menambah tindakan.');
        }

        $validated = $this->validate_detail_rekam_medis($request);

        DB::table('detail_rekam_medis')->insert([
            'idrekam_medis' => $idrekam_medis,
            'idkode_tindakan_terapi' => $validated['idkode_tindakan_terapi'],
            'detail' => $validated['detail'],
        ]);

        return redirect()->route('Dokter.RekamMedis.detail-rekam-medis', $idrekam_medis)
            ->with('success', 'Tindakan berhasil ditambahkan');
    }

    public function update_detail($iddetail_rekam_medis, Request $request)
    {
        $detail = DB::table('detail_rekam_medis')
            ->where('iddetail_rekam_medis', $iddetail_rekam_medis)
            ->whereNull('deleted_at')
            ->first();

        if (!$detail) {
            return redirect()->back()->with('error', 'Detail rekam medis tidak ditemukan.');
        }

        $rekam_medis = DB::table('rekam_medis')
            ->where('idrekam_medis', $detail->idrekam_medis)
            ->whereNull('deleted_at')
            ->first();

        if (!$rekam_medis) {
            return redirect()->back()->with('error', 'Rekam medis tidak ditemukan.');
        }

        $current_idrole_user = DB::table('role_user')
            ->where('iduser', '=', auth()->id())
            ->where('idrole', '=', 2)
            ->value('idrole_user');

        if (!$current_idrole_user || $current_idrole_user != $rekam_medis->dokter_pemeriksa) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah tindakan.');
        }

        $validated = $this->validate_detail_rekam_medis($request);

        DB::table('detail_rekam_medis')
            ->where('iddetail_rekam_medis', $iddetail_rekam_medis)
            ->update([
                'idkode_tindakan_terapi' => $validated['idkode_tindakan_terapi'],
                'detail' => $validated['detail'],
            ]);

        return redirect()->route('Dokter.RekamMedis.detail-rekam-medis', $detail->idrekam_medis)
            ->with('success', 'Tindakan berhasil diperbarui');
    }

    public function delete_detail($iddetail_rekam_medis)
    {
        $detail = DB::table('detail_rekam_medis')
            ->where('iddetail_rekam_medis', $iddetail_rekam_medis)
            ->whereNull('deleted_at')
            ->first();

        if (!$detail) {
            return redirect()->back()->with('error', 'Detail rekam medis tidak ditemukan.');
        }

        $rekam_medis = DB::table('rekam_medis')
            ->where('idrekam_medis', $detail->idrekam_medis)
            ->whereNull('deleted_at')
            ->first();

        if (!$rekam_medis) {
            return redirect()->back()->with('error', 'Rekam medis tidak ditemukan.');
        }

        $current_idrole_user = DB::table('role_user')
            ->where('iduser', '=', auth()->id())
            ->where('idrole', '=', 2)
            ->value('idrole_user');

        if (!$current_idrole_user || $current_idrole_user != $rekam_medis->dokter_pemeriksa) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus tindakan.');
        }

        DB::table('detail_rekam_medis')
            ->where('iddetail_rekam_medis', $iddetail_rekam_medis)
            ->update([
                'deleted_at' => Carbon::now(),
                'deleted_by' => auth()->id(),
            ]);

        return redirect()->route('Dokter.RekamMedis.detail-rekam-medis', $detail->idrekam_medis)
            ->with('success', 'Tindakan berhasil dihapus');
    }
}
//cihuy