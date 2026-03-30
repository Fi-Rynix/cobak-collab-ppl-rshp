<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReservasiSaya_Controller extends Controller
{
    public function daftar_reservasi_saya()
    {
        $iduser = auth()->id();
        
        $reservasi_list = DB::table('temu_dokter as td')
            ->join('pet as p', 'td.idpet', '=', 'p.idpet')
            ->join('pemilik as pm', 'p.idpemilik', '=', 'pm.idpemilik')
            ->where('pm.iduser', $iduser)
            ->whereNull('td.deleted_at')
            ->whereNull('td.deleted_by')
            ->select(
                'td.idreservasi_dokter',
                'td.no_urut',
                'td.status',
                'td.waktu_daftar',
                'p.idpet',
                'p.nama as pet_nama',
                'td.idrole_user'
            )
            ->orderBy('td.waktu_daftar', 'desc')
            ->get();

        $reservasi_list = $reservasi_list->map(function($item) {
            $dokter = DB::table('role_user as ru')
                ->join('user as u', 'ru.iduser', '=', 'u.iduser')
                ->where('ru.idrole_user', $item->idrole_user)
                ->select('u.nama')
                ->first();
            $item->dokter_nama = $dokter ? $dokter->nama : '-';
            return $item;
        });

        return view('Pemilik.TemuDokter.reservasi-saya', [
            'reservasi_list' => $reservasi_list
        ]);
    }
}
//cihuy