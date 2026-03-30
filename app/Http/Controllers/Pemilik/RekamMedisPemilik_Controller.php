<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RekamMedisPemilik_Controller extends Controller
{
    public function daftar_rekam_medis()
    {
        $iduser = auth()->id();
        
        $rekam_medis_list = DB::table('rekam_medis as rm')
            ->join('temu_dokter as td', 'rm.idreservasi_dokter', '=', 'td.idreservasi_dokter')
            ->join('pet as p', 'td.idpet', '=', 'p.idpet')
            ->join('pemilik as pm', 'p.idpemilik', '=', 'pm.idpemilik')
            ->where('pm.iduser', $iduser)
            ->whereNull('rm.deleted_at')
            ->whereNull('rm.deleted_by')
            ->select(
                'rm.idrekam_medis',
                'rm.anamnesa',
                'rm.temuan_klinis',
                'rm.diagnosa',
                'rm.created_at',
                'p.idpet',
                'p.nama as pet_nama',
                'rm.dokter_pemeriksa'
            )
            ->orderBy('rm.created_at', 'desc')
            ->get();

        $rekam_medis_list = $rekam_medis_list->map(function($item) {
            $dokter = DB::table('role_user as ru')
                ->join('user as u', 'ru.iduser', '=', 'u.iduser')
                ->where('ru.idrole_user', $item->dokter_pemeriksa)
                ->select('u.nama')
                ->first();
            $item->dokter_nama = $dokter ? $dokter->nama : '-';
            
            $detail = DB::table('detail_rekam_medis as drm')
                ->join('kode_tindakan_terapi as ktt', 'drm.idkode_tindakan_terapi', '=', 'ktt.idkode_tindakan_terapi')
                ->where('drm.idrekam_medis', $item->idrekam_medis)
                ->whereNull('drm.deleted_at')
                ->whereNull('drm.deleted_by')
                ->select('drm.detail', 'ktt.kode', 'ktt.deskripsi_tindakan_terapi')
                ->get();
            
            $item->detail_list = $detail;
            
            return $item;
        });

        return view('Pemilik.RekamMedis.rekam-medis', [
            'rekam_medis_list' => $rekam_medis_list
        ]);
    }

    public function detail_rekam_medis($id)
    {
        $iduser = auth()->id();
        
        $rekam_medis = DB::table('rekam_medis as rm')
            ->join('temu_dokter as td', 'rm.idreservasi_dokter', '=', 'td.idreservasi_dokter')
            ->join('pet as p', 'td.idpet', '=', 'p.idpet')
            ->join('pemilik as pm', 'p.idpemilik', '=', 'pm.idpemilik')
            ->where('rm.idrekam_medis', $id)
            ->where('pm.iduser', $iduser)
            ->whereNull('rm.deleted_at')
            ->whereNull('rm.deleted_by')
            ->select(
                'rm.idrekam_medis',
                'rm.anamnesa',
                'rm.temuan_klinis',
                'rm.diagnosa',
                'rm.created_at',
                'p.idpet',
                'p.nama as pet_nama',
                'rm.dokter_pemeriksa'
            )
            ->first();

        if (!$rekam_medis) {
            abort(404);
        }

        $dokter = DB::table('role_user as ru')
            ->join('user as u', 'ru.iduser', '=', 'u.iduser')
            ->where('ru.idrole_user', $rekam_medis->dokter_pemeriksa)
            ->select('u.nama')
            ->first();
        $rekam_medis->dokter_nama = $dokter ? $dokter->nama : '-';

        $detail_list = DB::table('detail_rekam_medis as drm')
            ->join('kode_tindakan_terapi as ktt', 'drm.idkode_tindakan_terapi', '=', 'ktt.idkode_tindakan_terapi')
            ->where('drm.idrekam_medis', $id)
            ->whereNull('drm.deleted_at')
            ->whereNull('drm.deleted_by')
            ->select('drm.detail', 'ktt.kode', 'ktt.deskripsi_tindakan_terapi')
            ->get();

        $rekam_medis->detail_list = $detail_list;

        return view('Pemilik.RekamMedis.detail-rekam-medis', [
            'rekam_medis' => $rekam_medis
        ]);
    }
}
//cihuy