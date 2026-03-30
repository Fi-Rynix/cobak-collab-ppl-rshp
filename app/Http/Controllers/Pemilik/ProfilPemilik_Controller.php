<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProfilPemilik_Controller extends Controller
{
    public function profil_saya()
    {
        $iduser = auth()->id();
        
        $user = DB::table('user')
            ->where('iduser', $iduser)
            ->first();
        
        $pemilik = DB::table('pemilik')
            ->where('iduser', $iduser)
            ->first();
        
        if (!$user || !$pemilik) {
            abort(404);
        }
        
        $pet_list = DB::table('pet as p')
            ->join('ras_hewan as rh', 'p.idras_hewan', '=', 'rh.idras_hewan')
            ->join('jenis_hewan as jh', 'rh.idjenis_hewan', '=', 'jh.idjenis_hewan')
            ->where('p.idpemilik', $pemilik->idpemilik)
            ->whereNull('p.deleted_at')
            ->whereNull('p.deleted_by')
            ->select(
                'p.idpet',
                'p.nama',
                'p.tanggal_lahir',
                'p.jenis_kelamin',
                'p.warna_tanda',
                'jh.nama_jenis_hewan as jenis_hewan_nama',
                'rh.nama_ras as ras_hewan_nama'
            )
            ->get();

        return view('Pemilik.Profil.profil-saya', [
            'user' => $user,
            'pemilik' => $pemilik,
            'pet_list' => $pet_list
        ]);
    }
}
//cihuy