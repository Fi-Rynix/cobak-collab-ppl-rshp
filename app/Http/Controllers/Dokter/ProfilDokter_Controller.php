<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProfilDokter_Controller extends Controller
{
    public function profil_saya()
    {
        $user = DB::table('user')
            ->where('iduser', auth()->id())
            ->first();

        $dokter = DB::table('dokter')
            ->where('iduser', auth()->id())
            ->whereNull('deleted_at')
            ->first();

        if (!$user || !$dokter) {
            abort(404);
        }

        return view('Dokter.Profil.profil-saya', [
            'user' => $user,
            'dokter' => $dokter,
        ]);
    }
}
//cihuy